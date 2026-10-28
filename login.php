
<?php
session_start();

if (isset($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/conexion.php';

$error = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
        $error = 'Escribe un correo válido y tu contraseña.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, nombre, correo, clave_hash
             FROM usuarios
             WHERE correo = ?
             LIMIT 1'
        );

        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($clave, $usuario['clave_hash'])) {
            session_regenerate_id(true);

            $_SESSION['uid'] = (int) $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - AgendaWeb</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --fondo: #FFF9FA;
            --fondo-rosa: #F8E8EC;
            --rosa: #E8B7C0;
            --rosa-claro: #F7E9EC;
            --tinto: #7A2636;
            --tinto-oscuro: #5A1824;
            --tinto-suave: #9B3F52;
            --blanco: #FFFFFF;
            --texto: #3F3034;
            --texto-suave: #76666B;
            --borde: #E2CDD2;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        .glow {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.55;
            z-index: -1;
            pointer-events: none;
        }

        .glow-1 {
            background: var(--rosa);
            top: -100px;
            left: -80px;
        }

        .glow-2 {
            background: var(--fondo-rosa);
            bottom: -120px;
            right: -80px;
        }

        .login-contenedor {
            width: 100%;
            max-width: 1000px;
            min-height: 560px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--blanco);
            border: 1px solid var(--borde);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(122, 38, 54, 0.12);
        }

        /* Panel izquierdo */
        .login-presentacion {
            background: linear-gradient(
                145deg,
                var(--tinto-oscuro),
                var(--tinto)
            );
            color: var(--blanco);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 13px;
            position: relative;
            z-index: 1;
        }

        .marca-icono {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            font-size: 22px;
        }

        .marca h2 {
            font-size: 23px;
            font-weight: 700;
        }

        .marca h2 span {
            color: var(--rosa);
        }

        .marca p {
            margin-top: 5px;
            font-size: 12px;
            color: var(--fondo-rosa);
        }

        .presentacion-texto {
            position: relative;
            z-index: 1;
            margin: 40px 0;
        }

        .etiqueta {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 11px;
            letter-spacing: 1px;
            margin-bottom: 22px;
        }

        .presentacion-texto h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .presentacion-texto p {
            color: var(--fondo-rosa);
            line-height: 1.8;
            font-size: 14px;
        }

        .calendario-decorativo {
            position: absolute;
            right: -20px;
            bottom: 100px;
            font-size: 150px;
            color: rgba(255, 255, 255, 0.08);
            transform: rotate(-12deg);
        }

        .presentacion-pie {
            font-size: 12px;
            color: var(--fondo-rosa);
            position: relative;
            z-index: 1;
        }

        .presentacion-pie i {
            color: var(--rosa);
            margin-right: 5px;
        }

        /* Formulario de inicio de sesión */
        .login-formulario {
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .icono-encabezado {
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fondo-rosa);
            color: var(--tinto);
            border: 1px solid var(--borde);
            border-radius: 18px;
            font-size: 24px;
            margin-bottom: 25px;
        }

        .login-formulario h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: var(--tinto);
            margin-bottom: 10px;
        }

        .subtitulo {
            color: var(--texto-suave);
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;
            color: var(--texto);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        .campo label i {
            color: var(--tinto-suave);
            margin-right: 7px;
        }

        .campo input {
            width: 100%;
            height: 48px;
            padding: 0 15px;
            border: 1px solid var(--borde);
            border-radius: 11px;
            outline: none;
            background: var(--fondo);
            color: var(--texto);
            font-family: inherit;
            font-size: 13px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .campo input::placeholder {
            color: #A39499;
        }

        .campo input:focus {
            border-color: var(--tinto-suave);
            box-shadow: 0 0 0 3px rgba(232, 183, 192, 0.3);
        }

        /* Botón para iniciar sesión */
        .btn-entrar {
            width: 100%;
            min-height: 49px;
            border: none;
            border-radius: 11px;
            background: var(--tinto);
            color: var(--blanco);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-entrar:hover {
            background: var(--tinto-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(122, 38, 54, 0.2);
        }

        .btn-entrar:focus-visible {
            outline: 3px solid var(--rosa);
            outline-offset: 3px;
        }

        .btn-entrar i {
            margin-left: 8px;
        }

        /* Mensaje de error */
        .error {
            padding: 12px 14px;
            margin-bottom: 20px;
            background: var(--fondo-rosa);
            color: var(--tinto-oscuro);
            border: 1px solid var(--rosa);
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Enlace de registro */
        .registro {
            text-align: center;
            font-size: 12px;
            color: var(--texto-suave);
            margin-top: 25px;
        }

        .registro a {
            color: var(--tinto);
            font-weight: 700;
            text-decoration: none;
        }

        .registro a:hover {
            color: var(--tinto-oscuro);
            text-decoration: underline;
        }

        .seguridad {
            margin-top: 28px;
            text-align: center;
            color: var(--texto-suave);
            font-size: 11px;
        }

        .seguridad i {
            color: var(--tinto-suave);
            margin-right: 5px;
        }

        /* Adaptación a celulares y tabletas */
        @media (max-width: 700px) {
            body {
                padding: 15px;
                overflow-y: auto;
            }

            .login-contenedor {
                max-width: 440px;
                grid-template-columns: 1fr;
            }

            .login-presentacion {
                padding: 28px;
                min-height: 240px;
            }

            .presentacion-texto {
                margin: 30px 0 15px;
            }

            .presentacion-texto h1 {
                font-size: 29px;
            }

            .presentacion-pie,
            .calendario-decorativo {
                display: none;
            }

            .login-formulario {
                padding: 35px 28px;
            }
        }
    </style>
</head>

<body>
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <main class="login-contenedor">

        <section class="login-presentacion">
            <div class="marca">
                <div class="marca-icono">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>

                <div>
                    <h2>Agenda<span>Web</span></h2>
                    <p>Organiza tus momentos</p>
                </div>
            </div>

            <div class="presentacion-texto">
                <span class="etiqueta">
                    <i class="fa-solid fa-sparkles"></i>
                    TU TIEMPO, MEJOR ORGANIZADO
                </span>

                <h1>Todo lo importante, en un solo lugar.</h1>

                <p>
                    Inicia sesión para administrar tus eventos,
                    organizar tus actividades y mantener tus citas
                    siempre al día.
                </p>
            </div>

            <div class="presentacion-pie">
                <i class="fa-solid fa-heart"></i>
                AgendaWeb · Organiza tu tiempo
            </div>

            <i class="fa-solid fa-calendar-check calendario-decorativo"></i>
        </section>

        <section class="login-formulario">
            <div class="icono-encabezado">
                <i class="fa-solid fa-user-lock"></i>
            </div>

            <h2>¡Qué gusto verte!</h2>

            <p class="subtitulo">
                Ingresa tus datos para acceder a tu agenda personal.
            </p>

            <?php if ($error !== ''): ?>
                <div class="error" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="campo">
                    <label for="correo">
                        <i class="fa-solid fa-envelope"></i>
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="tucorreo@ejemplo.com"
                        value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="clave">
                        <i class="fa-solid fa-lock"></i>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        placeholder="Escribe tu contraseña"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-entrar">
                    Iniciar sesión
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="registro">
                ¿Todavía no tienes cuenta?
                <a href="registro.php">Crear cuenta</a>
            </p>

            <div class="seguridad">
                <i class="fa-solid fa-shield-halved"></i>
                Acceso protegido a tu cuenta
            </div>
        </section>

    </main>
</body>
</html>
