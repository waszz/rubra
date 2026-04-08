<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso denegado &mdash; Rubra</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,900&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="/images/logo.png">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0a0a0a;
            color: #e5e5e5;
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 70% 50% at 50% 0%, rgba(139,0,0,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .lock-wrap {
            position: relative;
            width: 100px; height: 100px;
            margin-bottom: 32px;
        }
        .lock-circle {
            width: 100px; height: 100px;
            border-radius: 50%;
            border: 2px solid #2a2a2a;
            display: flex; align-items: center; justify-content: center;
            background: #111;
        }
        .lock-circle svg {
            width: 40px; height: 40px;
            color: #d15330;
        }
        .lock-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 1px solid rgba(209,83,48,0.2);
            animation: pulse-ring 2.5s ease-in-out infinite;
        }
        .lock-ring-2 {
            position: absolute;
            inset: -18px;
            border-radius: 50%;
            border: 1px solid rgba(209,83,48,0.1);
            animation: pulse-ring 2.5s ease-in-out infinite 0.5s;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 0; transform: scale(0.95); }
            50%       { opacity: 1; transform: scale(1); }
        }

        .particles { position: fixed; inset: 0; pointer-events: none; }
        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: #8b0000;
            border-radius: 50%;
            opacity: 0;
            animation: float var(--d, 7s) var(--delay, 0s) ease-in-out infinite;
        }
        @keyframes float {
            0%   { opacity: 0;   transform: translate(0,0) scale(1); }
            20%  { opacity: 0.4; }
            100% { opacity: 0;   transform: translate(var(--fx, 30px), var(--fy, -70px)) scale(0.3); }
        }

        .code {
            font-size: 10px; font-weight: 900;
            letter-spacing: 0.5em; text-transform: uppercase;
            color: #d15330; margin-bottom: 16px;
        }
        h1 {
            font-size: clamp(18px, 3.5vw, 26px); font-weight: 900;
            color: #fff; text-align: center; line-height: 1.25; margin-bottom: 12px;
        }
        p {
            font-size: 13px; color: #555; text-align: center;
            max-width: 340px; line-height: 1.7; margin-bottom: 32px;
        }
        .divider {
            width: 32px; height: 2px; background: #2a2a2a;
            border-radius: 2px; margin: 0 auto 24px;
        }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            background: #1a1a1a; color: #fff; font-size: 11px; font-weight: 900;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding: 12px 28px; border-radius: 8px; text-decoration: none;
            border: 1px solid #2a2a2a; transition: all 0.2s;
        }
        .btn-primary:hover { border-color: #444; background: #222; }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            color: #444; font-size: 11px; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            text-decoration: none; transition: color 0.2s;
        }
        .btn-secondary:hover { color: #888; }
        .logo-nav {
            position: fixed; top: 24px; left: 32px;
            font-size: 18px; font-weight: 900; letter-spacing: 0.15em;
            color: #fff; text-decoration: none; text-transform: uppercase; opacity: 0.3;
        }
        .logo-nav span { color: #d15330; }
    </style>
</head>
<body>

    <a href="/dashboard" class="logo-nav">R<span>.</span>ubra</a>

    <div class="particles">
        <div class="particle" style="left:20%;top:65%;--d:8s;--delay:0s;  --fx:40px; --fy:-100px;"></div>
        <div class="particle" style="left:40%;top:35%;--d:6s;--delay:.6s; --fx:-20px;--fy:-80px;"></div>
        <div class="particle" style="left:65%;top:55%;--d:9s;--delay:.2s; --fx:30px; --fy:-60px;"></div>
        <div class="particle" style="left:80%;top:25%;--d:7s;--delay:1s;  --fx:-40px;--fy:-90px;"></div>
        <div class="particle" style="left:10%;top:45%;--d:6s;--delay:1.4s;--fx:50px; --fy:-50px;"></div>
    </div>

    <div class="lock-wrap">
        <div class="lock-ring-2"></div>
        <div class="lock-ring"></div>
        <div class="lock-circle">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
    </div>

    <div class="code">Error 403 — Acceso denegado</div>
    <h1>No tenés permiso<br>para acceder aquí</h1>
    <div class="divider"></div>
    <p>Esta sección no está disponible para tu cuenta. Si creés que es un error, contactá al administrador.</p>

    <div class="actions">
        <a href="/dashboard" class="btn-primary">&#8592; Volver al dashboard</a>
        <a href="javascript:history.back()" class="btn-secondary">Atrás</a>
    </div>

</body>
</html>