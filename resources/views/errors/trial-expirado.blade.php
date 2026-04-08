<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plan expirado &mdash; Rubra</title>
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
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(209,83,48,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        #canvas-403 {
            display: block;
            width: 280px;
            height: 280px;
            margin-bottom: 20px;
        }

        .particles { position: fixed; inset: 0; pointer-events: none; }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            background: #d15330;
            border-radius: 50%;
            opacity: 0;
            animation: float var(--d, 6s) var(--delay, 0s) ease-in-out infinite;
        }
        @keyframes float {
            0%   { opacity: 0;   transform: translate(0,0) scale(1); }
            20%  { opacity: 0.6; }
            100% { opacity: 0;   transform: translate(var(--fx, 40px), var(--fy, -80px)) scale(0.3); }
        }

        .code {
            font-size: 11px; font-weight: 900;
            letter-spacing: 0.4em; text-transform: uppercase;
            color: #d15330; margin-bottom: 16px;
        }
        h1 {
            font-size: clamp(20px, 4vw, 28px); font-weight: 900;
            color: #fff; text-align: center; line-height: 1.2; margin-bottom: 12px;
        }
        p {
            font-size: 13px; color: #666; text-align: center;
            max-width: 360px; line-height: 1.7; margin-bottom: 32px;
        }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            background: #d15330; color: #fff; font-size: 11px; font-weight: 900;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding: 12px 28px; border-radius: 8px; text-decoration: none; transition: background 0.2s;
        }
        .btn-primary:hover { background: #b84426; }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent; color: #555; font-size: 11px; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase;
            padding: 12px 24px; border: 1px solid #222; border-radius: 8px;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-secondary:hover { color: #fff; border-color: #444; }
        .divider {
            width: 40px; height: 2px; background: #d15330;
            border-radius: 2px; margin: 0 auto 24px; opacity: 0.5;
        }
        .logo-nav {
            position: fixed; top: 24px; left: 32px;
            font-size: 18px; font-weight: 900; letter-spacing: 0.15em;
            color: #fff; text-decoration: none; text-transform: uppercase; opacity: 0.4;
        }
        .logo-nav span { color: #d15330; }
    </style>
</head>
<body>

    <a href="/dashboard" class="logo-nav">R<span>.</span>ubra</a>

    <div class="particles">
        <div class="particle" style="left:15%;top:70%;--d:7s;--delay:0s;  --fx:50px; --fy:-120px;"></div>
        <div class="particle" style="left:30%;top:40%;--d:5s;--delay:.8s; --fx:-30px;--fy:-90px;"></div>
        <div class="particle" style="left:60%;top:60%;--d:8s;--delay:.3s; --fx:40px; --fy:-60px;"></div>
        <div class="particle" style="left:75%;top:30%;--d:6s;--delay:1.2s;--fx:-60px;--fy:-100px;"></div>
        <div class="particle" style="left:85%;top:80%;--d:9s;--delay:.5s; --fx:20px; --fy:-140px;"></div>
        <div class="particle" style="left:45%;top:20%;--d:6s;--delay:2s;  --fx:-40px;--fy:-80px;"></div>
        <div class="particle" style="left:10%;top:50%;--d:7s;--delay:1.5s;--fx:70px; --fy:-50px;"></div>
        <div class="particle" style="left:90%;top:55%;--d:5s;--delay:.9s; --fx:-50px;--fy:-70px;background:#ff6b35;"></div>
    </div>

    <canvas id="canvas-403"></canvas>

    <div class="code">Plan expirado</div>
    <h1>Tu período de prueba<br>ha vencido</h1>
    <div class="divider"></div>
    <p>
        Estás en modo solo-lectura. Podés ver todos tus proyectos y datos,
        pero para hacer cambios necesitás activar un plan.
    </p>
    <div class="actions">
        <a href="/planes/basico/checkout" class="btn-primary">&#8593; Activar plan</a>
        <a href="/dashboard" class="btn-secondary">&#8592; Volver al dashboard</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/SVGLoader.js"></script>
    <script>
    (function () {
        const canvas = document.getElementById('canvas-403');

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 100);
        camera.position.set(0, 0, 7.5);

        const renderer = new THREE.WebGLRenderer({
            canvas: canvas,
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance'
        });
        renderer.setSize(280, 280);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.outputColorSpace = THREE.SRGBColorSpace;
        renderer.toneMapping = THREE.ReinhardToneMapping;
        renderer.toneMappingExposure = 1.0;

        const svgMarkup = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 885.75 885.75"><path d="M 29.992188 77.976562 L 470.867188 77.976562 C 593.832031 77.976562 695.804688 178.449219 695.804688 305.914062 C 695.804688 392.890625 643.320312 446.125 528.601562 508.355469 L 874.253906 797.773438 C 881.75 803.773438 883.25 809.023438 878.753906 813.519531 C 876.753906 815.019531 863.507812 815.769531 839.011719 815.769531 L 377.144531 428.878906 C 372.644531 425.378906 372.644531 421.382812 377.144531 416.882812 L 541.347656 307.414062 C 541.347656 251.929688 503.109375 209.941406 447.625 209.941406 L 29.992188 209.941406 C 21.992188 209.941406 17.996094 205.941406 17.996094 197.945312 L 17.996094 89.972656 C 17.996094 81.976562 21.992188 77.976562 29.992188 77.976562 Z" /><path d="M 24.742188 247.429688 L 196.445312 247.429688 C 205.441406 247.429688 209.941406 251.929688 209.941406 260.925781 L 209.941406 498.609375 L 24.742188 617.824219 C 16.246094 617.824219 11.996094 613.578125 11.996094 605.078125 L 11.996094 260.175781 C 11.996094 251.679688 16.246094 247.429688 24.742188 247.429688 Z" /><path d="M 13.496094 659.8125 L 13.496094 802.273438 C 13.496094 811.269531 17.996094 815.769531 26.992188 815.769531 L 196.445312 815.769531 C 205.441406 815.769531 209.941406 811.269531 209.941406 802.273438 L 209.941406 554.09375 C 209.941406 545.597656 205.691406 541.347656 197.195312 541.347656 Z" /><path d="M 237.683594 503.109375 L 334.40625 442.375 C 337.40625 440.375 340.402344 441.125 343.402344 444.625 L 769.28125 803.773438 C 775.28125 808.773438 775.28125 812.769531 769.28125 815.769531 L 580.335938 815.769531 L 236.183594 521.851562 C 232.683594 518.355469 230.933594 514.355469 230.933594 509.855469 C 230.933594 506.855469 233.183594 504.609375 237.683594 503.109375 Z" /></svg>';

        const frontMaterial = new THREE.MeshStandardMaterial({ color: 0xD15330, metalness: 0.05, roughness: 0.55, side: THREE.DoubleSide });
        const sideMaterial  = new THREE.MeshStandardMaterial({ color: 0xA83D1E, metalness: 0.05, roughness: 0.65, side: THREE.DoubleSide });
        const edgeMaterial  = new THREE.LineBasicMaterial({ color: 0xffd2c5, transparent: true, opacity: 0.22 });

        const extrudeSettings = { depth: 90, bevelEnabled: true, bevelSegments: 8, steps: 1, bevelSize: 10, bevelThickness: 10, curveSegments: 32 };

        const group = new THREE.Group();
        scene.add(group);
        const logoGroup = new THREE.Group();
        group.add(logoGroup);

        const svgData = new THREE.SVGLoader().parse(svgMarkup);
        svgData.paths.forEach(path => {
            THREE.SVGLoader.createShapes(path).forEach(shape => {
                const geo = new THREE.ExtrudeGeometry(shape, extrudeSettings);
                logoGroup.add(new THREE.Mesh(geo, [frontMaterial, sideMaterial]));
                logoGroup.add(new THREE.LineSegments(new THREE.EdgesGeometry(geo, 35), edgeMaterial));
            });
        });

        logoGroup.scale.set(0.0075, -0.0075, 0.0075);
        const box = new THREE.Box3().setFromObject(logoGroup);
        logoGroup.position.sub(box.getCenter(new THREE.Vector3()));
        logoGroup.rotation.x = -0.32;
        logoGroup.rotation.y = 0.58;
        logoGroup.rotation.z = -0.03;

        scene.add(new THREE.AmbientLight(0xffffff, 0.5));
        const k = new THREE.DirectionalLight(0xffffff, 3.5); k.position.set(3, 8, 10); scene.add(k);
        const t = new THREE.DirectionalLight(0xffffff, 1.5); t.position.set(0, 12, 4);  scene.add(t);
        const f = new THREE.DirectionalLight(0xff5522, 1.2); f.position.set(-8, 0, 5);  scene.add(f);
        const r = new THREE.PointLight(0xcc3300, 25, 20);    r.position.set(0, -4, -6); scene.add(r);

        (function animate() {
            requestAnimationFrame(animate);
            const now = performance.now();
            group.rotation.y += 0.004;
            group.rotation.x = Math.sin(now * 0.0005) * 0.06;
            group.position.y = Math.sin(now * 0.001) * 0.12;
            renderer.render(scene, camera);
        })();
    })();
    </script>

</body>
</html>
