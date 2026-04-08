
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invitación — Rubra</title>
    <style>
        body { margin: 0; padding: 0; background: #0d0d0d; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #141414; border: 1px solid #222; border-radius: 16px; overflow: hidden; }
        .header { background: #e85d27; padding: 32px 40px; }
        .header-logo { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 0.05em; }
        .header-sub { font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 2px; letter-spacing: 0.1em; text-transform: uppercase; }
        .body { padding: 36px 40px; }
        .title { font-size: 20px; font-weight: 600; color: #f0f0f0; margin: 0 0 12px; }
        .text { font-size: 14px; color: #999; line-height: 1.7; margin: 0 0 24px; }
        .badge { display: inline-block; background: #e85d27; color: #fff; font-size: 12px; font-weight: 600;
                 padding: 4px 14px; border-radius: 999px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 24px; }
        .btn { display: block; text-align: center; background: #e85d27; color: #fff; text-decoration: none;
               font-size: 14px; font-weight: 600; padding: 14px 32px; border-radius: 10px; margin: 0 0 24px; }
        .note { font-size: 12px; color: #555; line-height: 1.6; }
        .footer { border-top: 1px solid #1e1e1e; padding: 20px 40px; font-size: 12px; color: #444; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">RUBRA</div>
        <div class="header-sub">Budgeting & Control</div>
    </div>

    <div class="body">

        @if($userExists)
            {{-- Usuario existente --}}
            <p class="title">Tenés acceso a nuevos proyectos</p>
            <p class="text">
                Te han otorgado acceso a proyectos en la plataforma Rubra.
                Ya podés ingresar con tu cuenta y verlos en tu panel.
            </p>
            <span class="badge">{{ ucfirst(str_replace('_', ' ', $rol)) }}</span>
            <br/>
            <a href="{{ url('/') }}" class="btn">Ir a Rubra →</a>
            <p class="note">
                Tu rol asignado es <strong style="color:#e85d27;">{{ ucfirst(str_replace('_', ' ', $rol)) }}</strong>.
                Si tenés alguna consulta, contactá a quien te invitó.
            </p>
        @else
            {{-- Usuario nuevo --}}
            <p class="title">Te invitaron a Rubra</p>
            <p class="text">
                Recibiste una invitación para unirte a la plataforma de gestión de proyectos Rubra.
                Hacé clic en el botón para crear tu cuenta — el link es válido por 7 días.
            </p>
            <span class="badge">{{ ucfirst(str_replace('_', ' ', $rol)) }}</span>
            <br/>
            <a href="{{ url('/registro?token=' . $token) }}" class="btn">Crear mi cuenta →</a>
            <p class="note">
                Si no esperabas esta invitación podés ignorar este correo.
                El link expira en 7 días.
            </p>
        @endif

    </div>

    <div class="footer">
        © {{ date('Y') }} Rubra — Budgeting & Control. Todos los derechos reservados.
    </div>

</div>
</body>
</html>