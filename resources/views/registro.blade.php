<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear cuenta</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg: #0b1020;
      --card: #111733;
      --text: #e8ecff;
      --muted:#a8b0d8;
      --primary:#6ea8ff;
      --primary-2:#4177ff;
      --error:#ff6b6b;
      --success:#42d392;
      --ring: rgba(110,168,255,.35);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; display:grid; place-items:center; min-height:100%;
      background: radial-gradient(1000px 600px at 20% -10%, #16224c 0%, transparent 60%) no-repeat,
                  radial-gradient(800px 500px at 120% 30%, #1b2a66 0%, transparent 55%) no-repeat,
                  var(--bg);
      color:var(--text); font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      line-height:1.45;
    }
    .card{
      width:min(92vw, 520px);
      background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);
      border-radius:16px; padding:28px 26px 26px;
      box-shadow: 0 20px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05);
      backdrop-filter: blur(6px);
    }
    h1{margin:0 0 8px; font-size:26px; font-weight:700; letter-spacing:.2px}
    .sub{color:var(--muted); margin:0 0 20px; font-size:14px}
    .alert{
      padding:10px 12px; border-radius:10px; font-size:14px; margin:10px 0 14px;
      border:1px solid transparent;
    }
    .alert.error{background:rgba(255,107,107,.08); border-color:rgba(255,107,107,.35); color:#ffdede}
    .alert.success{background:rgba(66,211,146,.08); border-color:rgba(66,211,146,.35); color:#d9ffea}
    label{display:block; font-size:13px; color:var(--muted); margin:14px 0 6px}
    input, select{
      width:100%; padding:12px 12px; border-radius:12px;
      background:#0f1530; border:1px solid rgba(255,255,255,.08);
      color:var(--text); font-size:15px; outline:none;
      transition:border .15s, box-shadow .15s, transform .02s;
    }
    input:focus, select:focus{border-color:var(--primary); box-shadow:0 0 0 6px var(--ring)}
    .row{display:grid; grid-template-columns:1fr; gap:12px}
    @media (min-width:560px){ .row{grid-template-columns:1.2fr 1fr} }
    .btn{
      margin-top:18px; width:100%; padding:12px 14px; font-size:15px; font-weight:600;
      border:none; border-radius:12px; color:#0b1020; background:linear-gradient(180deg,var(--primary),var(--primary-2));
      box-shadow: 0 12px 30px rgba(65,119,255,.35); cursor:pointer;
      transition: transform .05s ease, filter .15s ease;
    }
    .btn:active{transform: translateY(1px)}
    .meta{margin-top:14px; color:var(--muted); font-size:14px; text-align:center}
    .meta a{color:var(--primary); text-decoration:none}
    .meta a:hover{filter:brightness(1.1)}
  </style>
</head>
<body>
  <main class="card">
    <h1>Crear cuenta</h1>
    <p class="sub">Regístrate para hacer pedidos más rápido.</p>

    @if (session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert error">
        <ul style="margin:0 0 0 16px">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ url('/registro') }}">
      @csrf

      <label for="nombre">Nombre</label>
      <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" placeholder="Tu nombre" required>

      <div class="row">
        <div>
          <label for="correo">Correo</label>
          <input id="correo" name="correo" type="email" value="{{ old('correo') }}" placeholder="tucorreo@ejemplo.com" required>
        </div>
        <div>
          <label for="password">Contraseña</label>
          <input id="password" name="password" type="password" placeholder="Mínimo 5 caracteres" required>
        </div>
      </div>

      {{-- Si quien registra es admin, puede elegir rol; si no, el registro será cliente --}}
      @php $isAdmin = strtolower(session('user.rol', '')) === 'admin'; @endphp
      @if ($isAdmin)
        <label for="rol">Rol</label>
        <select id="rol" name="rol">
          <option value="cliente" {{ old('rol')==='cliente' ? 'selected' : '' }}>Cliente</option>
          <option value="mesero"  {{ old('rol')==='mesero'  ? 'selected' : '' }}>Mesero</option>
          <option value="cocinero"{{ old('rol')==='cocinero'? 'selected' : '' }}>Cocinero</option>
          <option value="admin"   {{ old('rol')==='admin'   ? 'selected' : '' }}>Admin</option>
        </select>
      @endif

      <button class="btn" type="submit">Crear cuenta</button>
    </form>

    <p class="meta">
      ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
    </p>
  </main>
</body>
</html>
