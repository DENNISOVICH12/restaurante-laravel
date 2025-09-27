<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b1020; --card:#111733; --text:#e8ecff; --muted:#a8b0d8;
      --primary:#6ea8ff; --primary-2:#4177ff; --ring:rgba(110,168,255,.35);
      --error:#ff6b6b; --success:#42d392;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; min-height:100%; display:grid; place-items:center;
      background: radial-gradient(1000px 600px at 20% -10%, #16224c 0%, transparent 60%) no-repeat,
                  radial-gradient(800px 500px at 120% 30%, #1b2a66 0%, transparent 55%) no-repeat,
                  var(--bg);
      color:var(--text); font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
    }
    .card{
      width:min(92vw, 520px);
      background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);
      border-radius:16px; padding:28px 26px 26px;
      box-shadow:0 20px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05);
      backdrop-filter: blur(6px);
    }
    h1{margin:0 0 8px; font-size:26px; font-weight:700; letter-spacing:.2px}
    .sub{color:var(--muted); margin:0 0 20px; font-size:14px}
    .alert{
      padding:10px 12px; border-radius:10px; font-size:14px; margin:10px 0 14px; border:1px solid transparent;
    }
    .alert.error{background:rgba(255,107,107,.08); border-color:rgba(255,107,107,.35); color:#ffdede}
    .alert.success{background:rgba(66,211,146,.08); border-color:rgba(66,211,146,.35); color:#d9ffea}
    label{display:block; font-size:13px; color:var(--muted); margin:14px 0 6px}
    input{
      width:100%; padding:12px 12px; border-radius:12px;
      background:#0f1530; border:1px solid rgba(255,255,255,.08);
      color:var(--text); font-size:15px; outline:none;
      transition:border .15s, box-shadow .15s, transform .02s;
    }
    input:focus{border-color:var(--primary); box-shadow:0 0 0 6px var(--ring)}
    .btn{
      margin-top:18px; width:100%; padding:12px 14px; font-size:15px; font-weight:600;
      border:none; border-radius:12px; color:#0b1020;
      background:linear-gradient(180deg,var(--primary),var(--primary-2));
      box-shadow: 0 12px 30px rgba(65,119,255,.35); cursor:pointer;
      transition: transform .05s ease, filter .15s ease;
    }
    .btn:active{transform: translateY(1px)}
    .row{display:flex; justify-content:space-between; align-items:center; margin-top:10px}
    .meta{margin-top:14px; color:var(--muted); font-size:14px; text-align:center}
    .meta a{color:var(--primary); text-decoration:none}
    .meta a:hover{filter:brightness(1.1)}
  </style>
</head>
<body>
  <main class="card">
    <h1>Iniciar sesión</h1>
    <p class="sub">Accede a tu cuenta para gestionar pedidos.</p>

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

    <form method="POST" action="{{ url('/login') }}">
      @csrf
      <label for="usuario">Usuario o correo</label>
      <input id="usuario" type="text" name="usuario" value="{{ old('usuario') }}" placeholder="Ingresa tu usuario o correo" autocomplete="username" required>

      <label for="password">Contraseña</label>
      <input id="password" type="password" name="password" placeholder="Tu contraseña" required>

      <button class="btn" type="submit">Entrar</button>
    </form>

    <p class="meta">
      ¿No tienes cuenta?
      <a href="{{ route('registro') }}">Regístrate</a>
    </p>
  </main>
</body>
</html>
