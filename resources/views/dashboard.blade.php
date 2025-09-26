<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <style>
    :root{
      --bg:#f6f7fb;--card:#ffffff;--text:#111827;--muted:#6b7280;
      --primary:#0ea5e9;--primary-2:#0284c7;--border:#e5e7eb;
      --shadow:0 10px 30px rgba(2,6,23,.08)
    }
    *{box-sizing:border-box}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:var(--bg);color:var(--text)}
    .wrap{max-width:960px;margin:40px auto;background:var(--card);border-radius:14px;box-shadow:var(--shadow);overflow:hidden}
    header{padding:18px 24px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,var(--primary),var(--primary-2));color:#fff}
    .brand{display:flex;gap:10px;align-items:center;font-weight:700;letter-spacing:.2px}
    .user{font-weight:600;letter-spacing:.2px}
    .content{padding:26px 24px}
    h1{margin:0 0 8px 0;font-size:26px}
    p.muted{color:var(--muted);font-size:14px;margin:6px 0 18px 0}
    .row{display:flex;gap:12px;flex-wrap:wrap}
    a.btn{display:inline-flex;gap:8px;align-items:center;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:600;border:1px solid transparent;transition:transform .05s ease}
    a.btn:active{transform:translateY(1px)}
    .primary{background:var(--primary);color:#fff}
    .gray{background:#f3f4f6;color:var(--text);border-color:var(--border)}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:18px}
    .card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px}
    .card h3{margin:0 0 8px 0;font-size:16px}
    .card p{margin:0;color:var(--muted);font-size:14px;line-height:1.4}
    .stat{font-size:28px;font-weight:800;margin:10px 0}
    footer{padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;color:var(--muted);font-size:13px;background:#fafafa}
    @media (max-width:640px){.wrap{margin:18px}}
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div class="brand">🏠 <span>Dashboard</span></div>
      <div class="user">
        {{ session('user.nombre', 'Invitado') }} — Rol: {{ strtoupper(session('user.rol', 'CLIENTE')) }}
      </div>
    </header>

    <div class="content">
      <h1>¡Bienvenido!</h1>
      <p class="muted">Este es tu panel principal. Desde aquí puedes acceder rápido a acciones frecuentes.</p>

      <div class="row" style="margin-bottom:16px">
        <a href="/orden" class="btn primary">Hacer pedido</a>
        <a href="/logout" class="btn gray">Cerrar sesión</a>
      </div>

      <div class="cards">
        <div class="card">
          <h3>Pedidos recientes</h3>
          <div class="stat">3</div>
          <p>Últimas órdenes en proceso.</p>
        </div>
        <div class="card">
          <h3>Estado de entrega</h3>
          <div class="stat">2</div>
          <p>Pedidos en ruta.</p>
        </div>
        <div class="card">
          <h3>Historial</h3>
          <div class="stat">18</div>
          <p>Órdenes completadas.</p>
        </div>
      </div>
    </div>

    <footer>
      <span>© {{ date('Y') }} Restaurante</span>
      <span>Versión 1.0</span>
    </footer>
  </div>
</body>
</html>
