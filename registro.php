
<?php
session_start();

if (isset($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/conexion.php';

function e($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

$nombre = '';
$correo = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $clave = $_POST['clave'] ?? '';
    $clave2 = $_POST['clave2'] ?? '';

    if ($nombre === '' || strlen($nombre) > 80) {
        $error = 'Escribe tu nombre (máximo 80 caracteres).';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Escribe un correo válido.';
    } elseif (strlen($clave) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($clave !== $clave2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($clave, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (nombre, correo, clave_hash)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([$nombre, $correo, $hash]);

            header('Location: login.php?registrado=1');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $error = 'Ese correo ya tiene una cuenta.';
            } else {
                $error = 'No se pudo crear la cuenta. Revisa la conexión y la tabla usuarios.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta - AgendaWeb</title>

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
            padding: 30px 20px;
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        .decoracion {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(85px);
            opacity: 0.55;
            z-index: -1;
            pointer-events: none;
        }

        .decoracion-1 {
            background: var(--rosa);
            top: -110px;
            left: -90px;
        }

        .decoracion-2 {
            background: var(--fondo-rosa);
            bottom: -110px;
            right: -80px;
        }

        .registro-contenedor {
            width: 100%;
            max-width: 1000px;
            min-height: 620px;
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            background: var(--blanco);
            border: 1px solid var(--borde);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(122, 38, 54, 0.12);
        }

        /* Panel de presentación */
        .registro-presentacion {
            background: linear-gradient(
                145deg,
                var(--tinto-oscuro),
                var(--tinto)
            );
            color: var(--blanco);
            padding: 45px 35px;
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
            font-size: 10px;
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
            font-size: 13px;
        }

        .lista-beneficios {
            list-style: none;
            margin-top: 25px;
        }

        .lista-beneficios li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--fondo-rosa);
        }

        .lista-beneficios i {
            color: var(--rosa);
            font-size: 14px;
        }

        .icono-decorativo {
            position: absolute;
            right: -20px;
            bottom: 110px;
            font-size: 145px;
            color: rgba(255, 255, 255, 0.07);
            transform: rotate(-12deg);
        }

        .presentacion-pie {
            position: relative;
            z-index: 1;
            font-size: 12px;
            color: var(--fondo-rosa);
        }

        .presentacion-pie i {
            color: var(--rosa);
            margin-right: 5px;
        }

        /* Formulario */
        .registro-formulario {
            padding: 45px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .icono-encabezado {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fondo-rosa);
            color: var(--tinto);
            border: 1px solid var(--borde);
            border-radius: 17px;
            font-size: 23px;
            margin-bottom: 20px;
        }

        .registro-formulario h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: var(--tinto);
            margin-bottom: 10px;
        }

        .subtitulo {
            color: var(--texto-suave);
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .campo {
            margin-bottom: 17px;
        }

        .campo label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--texto);
        }

        .campo label i {
            color: var(--tinto-suave);
            margin-right: 7px;
            width: 14px;
        }

        .campo input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid var(--borde);
            border-radius: 10px;
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

        .ayuda {
            display: block;
            margin-top: 6px;
            color: var(--texto-suave);
            font-size: 10px;
        }

        /* Mensaje de error */
        .error {
            padding: 12px 14px;
            margin-bottom: 18px;
            background: var(--fondo-rosa);
            color: var(--tinto-oscuro);
            border: 1px solid var(--rosa);
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.5;
        }

        .error i {
            margin-right: 6px;
        }

        /* Botón */
        .btn-crear {
            width: 100%;
            min-height: 48px;
            border: none;
            border-radius: 11px;
            background: var(--tinto);
            color: var(--blanco);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 5px;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .btn-crear:hover {
            background: var(--tinto-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(122, 38, 54, 0.2);
        }

        .btn-crear i {
            margin-left: 8px;
        }

        .btn-crear:focus-visible {
            outline: 3px solid var(--rosa);
            outline-offset: 3px;
        }

        .iniciar-sesion {
            text-align: center;
            font-size: 12px;
            color: var(--texto-suave);
            margin-top: 23px;
        }

        .iniciar-sesion a {
            color: var(--tinto);
            font-weight: 700;
            text-decoration: none;
        }

        .iniciar-sesion a:hover {
            color: var(--tinto-oscuro);
            text-decoration: underline;
        }

        .seguridad {
            margin-top: 22px;
            text-align: center;
            color: var(--texto-suave);
            font-size: 11px;
        }

        .seguridad i {
            color: var(--tinto-suave);
            margin-right: 5px;
        }

        /* Diseño para pantallas pequeñas */
        @media (max-width: 760px) {
            body {
                padding: 15px;
                align-items: flex-start;
            }

            .registro-contenedor {
                max-width: 480px;
                grid-template-columns: 1fr;
            }

            .registro-presentacion {
                padding: 28px;
                min-height: 260px;
            }

            .presentacion-texto {
                margin: 30px 0 15px;
            }

            .presentacion-texto h1 {
                font-size: 30px;
            }

            .lista-beneficios {
                display: none;
            }

            .presentacion-pie,
            .icono-decorativo {
                display: none;
            }

            .registro-formulario {
                padding: 32px 25px;
            }
        }
    </style>
</head>

<body>
    <div class="decoracion decoracion-1"></div>
    <div class="decoracion decoracion-2"></div>

    <main class="registro-contenedor">

        <section class="registro-presentacion">
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
                    <i class="fa-solid fa-heart"></i>
                    EMPIEZA HOY
                </span>

                <h1>Tu tiempo merece estar bien organizado.</h1>

                <p>
                    Crea tu cuenta y comienza a organizar tus eventos,
                    citas y actividades en un solo lugar.
                </p>

                <ul class="lista-beneficios">
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Organiza tus actividades.
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Administra tus próximos eventos.
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Accede a tu agenda personal.
                    </li>
                </ul>
            </div>

            <div class="presentacion-pie">
                <i class="fa-solid fa-heart"></i>
                AgendaWeb · Organiza tu tiempo
            </div>

            <i class="fa-solid fa-calendar-check icono-decorativo"></i>
        </section>

        <section class="registro-formulario">
            <div class="icono-encabezado">
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <h2>Crear cuenta</h2>

            <p class="subtitulo">
                Completa tus datos para comenzar a utilizar AgendaWeb.
            </p>

            <?php if ($error !== ''): ?>
                <div class="error" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="registro.php">
                <div class="campo">
                    <label for="nombre">
                        <i class="fa-solid fa-user"></i>
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="80"
                        placeholder="Escribe tu nombre"
                        value="<?= e($nombre) ?>"
                        autocomplete="name"
                        required
                    >
                </div>

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
                        value="<?= e($correo) ?>"
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
                        minlength="8"
                        placeholder="Crea una contraseña"
                        autocomplete="new-password"
                        required
                    >

                    <small class="ayuda">
                        Debe tener al menos 8 caracteres.
                    </small>
                </div>

                <div class="campo">
                    <label for="clave2">
                        <i class="fa-solid fa-shield-halved"></i>
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        id="clave2"
                        name="clave2"
                        minlength="8"
                        placeholder="Escribe nuevamente tu contraseña"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-crear">
                    Crear mi cuenta
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="iniciar-sesion">
                ¿Ya tienes cuenta?
                <a href="login.php">Iniciar sesión</a>
            </p>

            <div class="seguridad">
                <i class="fa-solid fa-shield-halved"></i>
                Tu contraseña se guarda de forma segura
            </div>
        </section>

    </main>
</body>
</html>
