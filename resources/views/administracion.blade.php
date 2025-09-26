<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administración</title>
  <style>
    :root{
      --bg:#f6f7fb;--card:#ffffff;--text:#111827;--muted:#6b7280;
      --primary:#10b981;--primary-2:#059669;--border:#e5e7eb;
      --danger:#ef4444;--warning:#f59e0b;--shadow:0 10px 30px rgba(2,6,23,.08)
    }
    *{box-sizing:border-box}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:var(--bg);color:var(--text)}
    .wrap{max-width:1100px;margin:40px auto;background:var(--card);border-radius:14px;box-shadow:var(--shadow);overflow:hidden}
    header{padding:18px 24px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,var(--primary),var(--primary-2));color:#fff}
    .brand{display:flex;gap:10px;align-items:center;font-weight:800;letter-spacing:.2px}
    .user{font-weight:600}
    .content{padding:26px 24px}
    h1{margin:0 0 8px 0;font-size:26px}
    p.muted{color:var(--muted);font-size:14px;margin:6px 0 18px 0}
    .row{display:flex;gap:12px;flex-wrap:wrap}
    a.btn{display:inline-flex;gap:8px;align-items:center;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:600;border:1px solid transparent;transition:transform .05s ease}
    a.btn:active{transform:translateY(1px)}
    .primary{background:var(--primary);color:#fff}
    .gray{background:#f3f4f6;color:var(--text);border-color:var(--border)}
    .danger{background:var(--danger);color:#fff}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;margin-top:18px}
    .card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px}
    .card h3{margin:0 0 8px 0;font-size:16px}
    .card p{margin:0;color:var(--muted);font-size:14px;line-height:1.4}
    .stat{font-size:28px;font-weight:800;margin:10px 0}
    .grid-2{display:grid;grid-template-columns:1.2fr .8fr;gap:12px;margin-top:12px}
    .table{width:100%;border-collapse:collapse;font-size:14px}
    .table th,.table td{padding:10px;border-bottom:1px solid var(--border);text-align:left}
    .badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700}
    .b-ok{background:#dcfce7;color:#065f46}
    .b-warn{background:#fef3c7;color:#92400e}
    .b-bad{background:#fee2e2;color:#991b1b}
    footer{padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;color:var(--muted);font-size:13px;background:#fafafa}
    @media (max-width:880px){.grid-2{grid-template-columns:1fr}}
    @media (max-width:640px){.wrap{margin:18px}}
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div class="brand">🛠️ <span>Panel de Administración</span></div>
      <div class="user">
        {{ session('user.nombre', 'Admin') }} — Rol: {{ strtoupper(session('user.rol', 'ADMIN')) }}
      </div>
    </header>

    <div class="content">
      <h1>Bienvenido, administrador</h1>
      <p class="muted">Desde aquí puedes gestionar usuarios, menú, pedidos y configuraciones.</p>

      <div class="row" style="margin-bottom:16px">
        <a href="/meseros" class="btn gray">Panel Meseros</a>
        <a href="/cocina" class="btn gray">Cocina</a>
        <a href="/dashboard" class="btn gray">Dashboard Cliente</a>
        <a href="/docs" class="btn primary">API Docs (Swagger)</a>
        <a href="/logout" class="btn danger">Cerrar sesión</a>
      </div>

      <!-- Tarjetas rápidas -->
      <div class="cards">
        <div class="card">
          <h3>Usuarios</h3>
          <div class="stat" id="stat-usuarios">—</div>
          <p>Gestiona creación, actualización y baja.</p>
          <div style="margin-top:12px">
            <a class="btn gray" href="/login">Crear / revisar</a>
          </div>
        </div>

        <div class="card">
          <h3>Ítems de Menú</h3>
          <div class="stat" id="stat-menu">—</div>
          <p>Platos, bebidas y postres disponibles.</p>
          <div style="margin-top:12px">
            <a class="btn gray" href="/docs#/Men%C3%BA">Abrir en Swagger</a>
          </div>
        </div>

        <div class="card">
          <h3>Pedidos</h3>
          <div class="stat" id="stat-pedidos">—</div>
          <p>Pedidos activos y últimos creados.</p>
          <div style="margin-top:12px">
            <a class="btn gray" href="/docs#/Pedidos">Abrir en Swagger</a>
          </div>
        </div>

        <div class="card">
          <h3>Configuraciones</h3>
          <div class="stat">⚙️</div>
          <p>Ajustes generales del sistema.</p>
          <div style="margin-top:12px">
            <a class="btn gray" href="#">Próximamente</a>
          </div>
        </div>
      </div>

      <!-- Dos columnas: últimos pedidos (datos reales) + estado servicios -->
      <div class="grid-2">
        <div class="card">
          <h3>Últimos pedidos</h3>
          <table class="table">
            <thead>
              <tr>
                <th>#</th><th>Cliente</th><th>Mesa</th><th>Estado</th><th>Total</th>
              </tr>
            </thead>
            <tbody id="tbody-pedidos"></tbody>
          </table>
          <div style="margin-top:12px">
            <a href="/docs#/Pedidos" class="btn primary">Ver/crear pedidos en API</a>
          </div>
        </div>

        <div class="card">
          <h3>Servicios</h3>
          <table class="table">
            <tbody>
              <tr><td>API Pedidos</td><td><span class="badge b-ok">OK</span></td></tr>
              <tr><td>Base de datos</td><td><span class="badge b-ok">OK</span></td></tr>
              <tr><td>Notificaciones</td><td><span class="badge b-warn">Degradado</span></td></tr>
              <tr><td>Impresión</td><td><span class="badge b-ok">OK</span></td></tr>
            </tbody>
          </table>
          <p class="muted">* Demo visual de estado. Integraremos chequeos reales más adelante.</p>
        </div>
      </div>
    </div>

    <footer>
      <span>© {{ date('Y') }} Restaurante</span>
      <span>Administración · v1.0</span>
    </footer>
  </div>

  <!-- Script que CONECTA con tu API y pinta los datos -->
  <script>
  (async () => {
    const j = async (url) => {
      const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!r.ok) throw new Error(\`\${r.status} \${r.statusText}\`);
      return r.json();
    };

    const put = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.textContent = (val ?? '—');
    };

    try {
      // Pedimos datos a TUS endpoints existentes
      const [usuarios, menu, pedidos] = await Promise.all([
        j('/api/usuarios?page=1'),
        j('/api/menu-items?page=1'),
        j('/api/pedidos?page=1')
      ]);

      // Contadores: intenta meta.total; si no existe, data.length
      put('stat-usuarios', usuarios?.meta?.total ?? usuarios?.data?.length ?? '—');
      put('stat-menu',     menu?.meta?.total     ?? menu?.data?.length     ?? '—');
      put('stat-pedidos',  pedidos?.meta?.total  ?? pedidos?.data?.length  ?? '—');

      // Tabla últimos pedidos (máximo 5)
      const tbody = document.getElementById('tbody-pedidos');
      if (tbody) {
        tbody.innerHTML = '';
        (pedidos?.data ?? []).slice(0, 5).forEach(row => {
          const estado = String(row.estado || '').toLowerCase();
          const badgeClass =
            estado === 'entregado'  ? 'b-ok'  :
            estado === 'en_entrega' ? 'b-warn':
            estado ? 'b-bad' : '';

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${row.id ?? ''}</td>
            <td>${row.cliente?.nombre_cliente ?? '-'}</td>
            <td>${row.mesa ?? '-'}</td>
            <td><span class="badge ${badgeClass}">${estado || '-'}</span></td>
            <td>${row.total ?? ''}</td>
          `;
          tbody.appendChild(tr);
        });
      }
    } catch (e) {
      console.error('Error cargando panel admin:', e);
      // si falla, dejamos los guiones
    }
  })();
  </script>
</body>
</html>
