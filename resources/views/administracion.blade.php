<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administración</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700&display=swap');

    :root {
      --bg: #f2f7ff;
      --panel: #ffffff;
      --text: #102a43;
      --muted: #486581;
      --highlight: #ffe066;
      --primary: #4c6ef5;
      --primary-dark: #364fc7;
      --success: #51cf66;
      --warning: #ffa94d;
      --danger: #ff6b6b;
      --shadow: 0 18px 45px rgba(15, 36, 80, 0.12);
      --radius: 22px;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Baloo 2", "Nunito", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: linear-gradient(160deg, #dbeafe 0%, #fdf2ff 55%, #fff 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .wrap {
      width: min(1100px, 100%);
      background: var(--panel);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: #fff;
      padding: 32px clamp(20px, 5vw, 48px);
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 14px;
      font-size: clamp(24px, 4vw, 34px);
      font-weight: 800;
      letter-spacing: 0.6px;
    }

    .brand span { display: block; }

    .user {
      font-size: clamp(16px, 2.3vw, 20px);
      font-weight: 600;
      background: rgba(255, 255, 255, 0.18);
      padding: 10px 18px;
      border-radius: 999px;
    }

    .content {
      padding: clamp(22px, 5vw, 48px);
      display: grid;
      gap: clamp(22px, 4vw, 36px);
    }

    .content h1 {
      font-size: clamp(24px, 5vw, 36px);
      margin: 0;
      line-height: 1.15;
    }

    .content p.muted {
      margin: 6px 0 0 0;
      color: var(--muted);
      font-size: clamp(16px, 3vw, 18px);
    }

    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
    }

    .action-card {
      background: linear-gradient(145deg, #fff6bf 0%, #fff 55%, #fff6bf 100%);
      border-radius: 18px;
      border: 2px dashed rgba(255, 193, 7, 0.35);
      padding: 18px;
      display: grid;
      gap: 12px;
      text-align: center;
      transition: transform 0.1s ease, box-shadow 0.1s ease;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      font-weight: 600;
      font-size: 16px;
    }

    .action-card small {
      font-size: 14px;
      color: var(--muted);
      font-weight: 500;
      display: block;
    }

    .action-card.blue {
      background: linear-gradient(145deg, rgba(76, 110, 245, 0.18) 0%, #fff 60%, rgba(76, 110, 245, 0.12) 100%);
      border-color: rgba(76, 110, 245, 0.35);
    }

    .action-card.green {
      background: linear-gradient(145deg, rgba(81, 207, 102, 0.18) 0%, #fff 60%, rgba(81, 207, 102, 0.12) 100%);
      border-color: rgba(81, 207, 102, 0.35);
    }

    .action-card.red {
      background: linear-gradient(145deg, rgba(255, 107, 107, 0.18) 0%, #fff 60%, rgba(255, 107, 107, 0.12) 100%);
      border-color: rgba(255, 107, 107, 0.35);
    }

    .action-card:hover,
    .action-card:focus-visible {
      transform: translateY(-3px);
      box-shadow: 0 16px 30px rgba(17, 42, 67, 0.18);
    }

    .action-card span.emoji { font-size: 34px; }

    .section-title {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: clamp(22px, 4vw, 28px);
      margin: 0;
    }

    .section-title span {
      background: var(--highlight);
      color: #1f2937;
      padding: 6px 14px;
      border-radius: 999px;
      font-weight: 700;
      font-size: clamp(14px, 3vw, 16px);
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 18px;
    }

    .card {
      background: #fff;
      border-radius: 18px;
      border: 2px solid rgba(16, 42, 67, 0.06);
      padding: 20px;
      box-shadow: 0 10px 24px rgba(15, 36, 80, 0.08);
      display: grid;
      gap: 8px;
      position: relative;
      overflow: hidden;
    }

    .card::after {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(circle at top right, rgba(76, 110, 245, 0.12), transparent 55%);
      pointer-events: none;
    }

    .card h3 {
      margin: 0;
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .stat {
      font-size: clamp(28px, 6vw, 36px);
      font-weight: 800;
      color: var(--primary-dark);
    }

    .card p {
      margin: 0;
      color: var(--muted);
      line-height: 1.4;
      font-size: 15px;
    }

    .card a.btn { margin-top: 8px; justify-self: start; }

    .grid-two {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 18px;
      align-items: start;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }

    th, td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid rgba(16, 42, 67, 0.08);
    }

    th {
      color: var(--muted);
      font-size: 14px;
      letter-spacing: 0.4px;
      text-transform: uppercase;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      text-transform: capitalize;
    }

    .b-ok { background: rgba(81, 207, 102, 0.18); color: #20744a; }
    .b-warn { background: rgba(255, 169, 77, 0.2); color: #995200; }
    .b-bad { background: rgba(255, 107, 107, 0.2); color: #a32020; }

    a.btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--primary);
      color: #fff;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 15px;
      transition: transform 0.1s ease, box-shadow 0.1s ease;
      box-shadow: 0 10px 18px rgba(76, 110, 245, 0.25);
    }

    a.btn:hover,
    a.btn:focus-visible {
      transform: translateY(-2px);
      box-shadow: 0 14px 22px rgba(76, 110, 245, 0.35);
    }

    footer {
      background: #f1f5f9;
      padding: 18px clamp(20px, 5vw, 48px);
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: space-between;
      align-items: center;
      color: var(--muted);
      font-size: 14px;
    }

    .hint-box {
      background: linear-gradient(145deg, rgba(255, 224, 102, 0.35) 0%, rgba(255, 241, 179, 0.7) 100%);
      border-radius: 20px;
      padding: 18px 22px;
      font-size: 16px;
      display: flex;
      gap: 14px;
      align-items: center;
      color: #8c6d1f;
      font-weight: 600;
    }

    .hint-box span { font-size: 28px; }

    @media (max-width: 720px) {
      body { padding: 16px; }
      header { padding: 26px 22px; }
      .content { padding: 26px 22px 32px; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div class="brand">🧩 <span>Panel súper fácil</span></div>
      <div class="user">
        {{ session('user.nombre', 'Admin') }} · Rol: {{ strtoupper(session('user.rol', 'ADMIN')) }}
      </div>
    </header>

    <div class="content">
      <div>
        <h1>¡Hola, capitán del restaurante! 👋</h1>
        <p class="muted">Elige lo que necesitas con los botones coloridos. Cada opción tiene un dibujito para hacerlo más sencillo.</p>
      </div>

      <div class="quick-actions">
        <a href="/meseros" class="action-card blue">
          <span class="emoji">🧑‍🍳</span>
          <span>Ir con los meseros</span>
          <small>Ver pedidos que llevan a las mesas.</small>
        </a>
        <a href="/cocina" class="action-card green">
          <span class="emoji">🍽️</span>
          <span>Ayudar en la cocina</span>
          <small>Revisa lo que se está cocinando.</small>
        </a>
        <a href="/dashboard" class="action-card blue">
          <span class="emoji">🧾</span>
          <span>Mirar el tablero</span>
          <small>Todo lo que ven los clientes.</small>
        </a>
        <a href="/docs" class="action-card green">
          <span class="emoji">📚</span>
          <span>Ver la API</span>
          <small>Explora los super poderes del sistema.</small>
        </a>
        <a href="/logout" class="action-card red">
          <span class="emoji">🚪</span>
          <span>Salir del panel</span>
          <small>Guarda tus avances antes de irte.</small>
        </a>
      </div>

      <div class="hint-box">
        <span>💡</span>
        <div>Tip: Si ves un número grande es porque todo marcha genial. Si aparece "—", intenta recargar la página.</div>
      </div>

      <div>
        <h2 class="section-title">🎯 <span>Vista rápida</span></h2>
      </div>

      <div class="cards">
        <div class="card">
          <h3>😊 Usuarios felices</h3>
          <div class="stat" id="stat-usuarios">—</div>
          <p>Crea, cambia o elimina usuarios en un par de clics.</p>
          <a class="btn" href="/login">Abrir panel de usuarios</a>
        </div>

        <div class="card">
          <h3>🍕 Menú delicioso</h3>
          <div class="stat" id="stat-menu">—</div>
          <p>Explora platos, bebidas y postres disponibles.</p>
          <a class="btn" href="/docs#/Men%C3%BA">Revisar en la API</a>
        </div>

        <div class="card">
          <h3>🛎️ Pedidos en camino</h3>
          <div class="stat" id="stat-pedidos">—</div>
          <p>Mira los pedidos activos o crea nuevos fácilmente.</p>
          <a class="btn" href="/docs#/Pedidos">Ver pedidos</a>
        </div>

        <div class="card">
          <h3>⚙️ Ajustes mágicos</h3>
          <div class="stat">✨</div>
          <p>Muy pronto podrás cambiar opciones especiales aquí.</p>
          <a class="btn" href="#">Pronto disponible</a>
        </div>
      </div>

      <div>
        <h2 class="section-title">🧺 <span>Lo que está pasando</span></h2>
      </div>

      <div class="grid-two">
        <div class="card">
          <h3>📦 Últimos pedidos</h3>
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Mesa</th>
                <th>Estado</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody id="tbody-pedidos"></tbody>
          </table>
          <a href="/docs#/Pedidos" class="btn">Crear o ver pedidos</a>
        </div>

        <div class="card">
          <h3>🔌 Servicios del restaurante</h3>
          <table>
            <tbody>
              <tr><td>API de pedidos</td><td><span class="badge b-ok">Todo bien</span></td></tr>
              <tr><td>Base de datos</td><td><span class="badge b-ok">Todo bien</span></td></tr>
              <tr><td>Notificaciones</td><td><span class="badge b-warn">Un poco lento</span></td></tr>
              <tr><td>Impresión</td><td><span class="badge b-ok">Todo bien</span></td></tr>
            </tbody>
          </table>
          <p class="muted">Estos indicadores son de ejemplo. ¡Pronto serán en tiempo real!</p>
        </div>
      </div>
    </div>

    <footer>
      <span>© {{ date('Y') }} Restaurante feliz</span>
      <span>Panel amigable · v1.1</span>
    </footer>
  </div>

  <script>
  (async () => {
    const j = async (url) => {
      const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!r.ok) throw new Error(`${r.status} ${r.statusText}`);
      return r.json();
    };

    const put = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.textContent = (val ?? '—');
    };

    const formatCurrency = (val) => {
