<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 28px 32px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 380px;
        }
        h1 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #111827;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.2);
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }
        button:hover {
            background-color: #1e40af;
        }
        .error {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1>Iniciar Sesión</h1>

    {{-- Mostrar mensaje de error si las credenciales fallan --}}
    @if ($errors->any())
        <div class="error">
            {{ $errors->first('usuario') }}
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" required autofocus>

        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>
