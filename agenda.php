
<?php
require_once __DIR__ . '/partes/sesion.php';
require_once __DIR__ . '/conexion.php';

/* =========================
   OBTENER USUARIO ACTUAL
   ========================= */
$usuarioId = (int) $_SESSION['uid'];

/* =========================
   OBTENER SOLO SUS EVENTOS
   ========================= */
$sql = "SELECT
    eventos.id,
    eventos.titulo,
    eventos.cliente,
    eventos.fecha,
    eventos.hora,
    categorias.nombre AS categoria,
    eventos.ubicacion,
    eventos.descripcion
    FROM eventos
    INNER JOIN categorias
        ON eventos.categoria_id = categorias.id
    WHERE eventos.usuario_id = :usuario_id
    ORDER BY eventos.fecha, eventos.hora";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':usuario_id' => $usuarioId
]);

$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Agenda - AgendaWeb</title>

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <!-- DECORACIÓN -->
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar">

        <!-- LOGO -->
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>

            <div>
                <h2>
                    Agenda<span>Web</span>
                </h2>
                <p>Organiza tus momentos</p>
            </div>
        </div>

        <!-- MENÚ -->
        <nav class="menu">
            <a href="index.php" class="menu-item">
                <i class="fa-solid fa-house"></i>
                <span>Inicio</span>
            </a>

            <a href="agenda.php" class="menu-item active">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Mi Agenda</span>
            </a>
        </nav>

        <!-- FRASE -->
        <div class="sidebar-frase">
            <i class="fa-solid fa-quote-left"></i>
            <p>
                "Organiza tu tiempo y disfruta cada momento."
            </p>
            <i class="fa-solid fa-quote-right"></i>
        </div>

        <!-- PIE -->
        <div class="sidebar-footer">
            <i class="fa-solid fa-heart"></i>
            <span>AgendaWeb</span>
        </div>

    </aside>

    <!-- ================= CONTENIDO PRINCIPAL ================= -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <div class="usuario">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

                <span>
                    Mi Agenda —
                    <?= htmlspecialchars(
                        $_SESSION['nombre'] ?? 'Usuario',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>
            </div>

            <a href="logout.php" class="btn-guardar">
                <i class="fa-solid fa-right-from-bracket"></i>
                Cerrar sesión
            </a>

        </header>

        <!-- ================= MI AGENDA ================= -->
        <div class="agenda-contenido">

            <!-- ENCABEZADO -->
            <section class="agenda-encabezado">

                <div class="agenda-titulo">
                    <p class="etiqueta">
                        <i class="fa-solid fa-calendar-check"></i>
                        MIS EVENTOS
                    </p>

                    <h2>Mi Agenda</h2>

                    <p class="texto-inicio">
                        Aquí puedes consultar, editar y eliminar tus eventos registrados.
                    </p>
                </div>

                <div class="agenda-derecha">

                    <!-- CONTADOR -->
                    <div class="contador-eventos">
                        <strong><?= count($eventos) ?></strong>
                        <span>
                            <?= count($eventos) === 1 ? 'evento' : 'eventos' ?>
                        </span>
                    </div>

                    <!-- NUEVO EVENTO -->
                    <a href="index.php" class="btn-nuevo">
                        <i class="fa-solid fa-plus"></i>
                        Nuevo evento
                    </a>

                </div>
            </section>

            <!-- ================= MENSAJES ================= -->

            <?php if (isset($_GET["guardado"])): ?>
                <div class="mensaje-exito">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>¡Evento guardado correctamente!</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET["actualizado"])): ?>
                <div class="mensaje-exito">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>¡Evento actualizado correctamente!</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET["eliminado"])): ?>
                <div class="mensaje-exito">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>¡Evento eliminado correctamente!</span>
                </div>
            <?php endif; ?>

            <!-- ================= EVENTOS ================= -->

            <?php if (count($eventos) === 0): ?>

                <!-- AGENDA VACÍA -->
                <section class="agenda-vacia">

                    <div class="agenda-vacia-icon">
                        <i class="fa-regular fa-calendar-xmark"></i>
                    </div>

                    <h3>No tienes eventos todavía</h3>

                    <p>
                        Comienza agregando tu primer evento a la agenda.
                    </p>

                    <a href="index.php" class="btn-guardar">
                        <i class="fa-solid fa-plus"></i>
                        Crear evento
                    </a>

                </section>

            <?php else: ?>

                <!-- GRID DE EVENTOS -->
                <section class="eventos-grid">

                    <?php foreach ($eventos as $evento): ?>

                        <article class="evento-card">

                            <!-- ICONO -->
                            <div class="evento-icono">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>

                            <!-- INFORMACIÓN -->
                            <div class="evento-contenido">

                                <!-- TÍTULO -->
                                <h3>
                                    <?= htmlspecialchars(
                                        $evento["titulo"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </h3>

                                <!-- CLIENTE -->
                                <?php if (!empty($evento["cliente"])): ?>
                                    <div class="evento-dato">
                                        <i class="fa-solid fa-user"></i>
                                        <span>
                                            <?= htmlspecialchars(
                                                $evento["cliente"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- FECHA -->
                                <?php if (!empty($evento["fecha"])): ?>
                                    <div class="evento-fecha">
                                        <i class="fa-regular fa-calendar"></i>
                                        <span>
                                            <?= htmlspecialchars(
                                                $evento["fecha"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- HORA -->
                                <?php if (!empty($evento["hora"])): ?>
                                    <div class="evento-dato">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>
                                            <?= htmlspecialchars(
                                                substr($evento["hora"], 0, 5),
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- CATEGORÍA -->
                                <?php if (!empty($evento["categoria"])): ?>
                                    <div class="evento-dato">
                                        <i class="fa-solid fa-tag"></i>
                                        <span>
                                            <?= htmlspecialchars(
                                                $evento["categoria"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- UBICACIÓN -->
                                <?php if (!empty($evento["ubicacion"])): ?>
                                    <div class="evento-dato">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>
                                            <?= htmlspecialchars(
                                                $evento["ubicacion"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- DESCRIPCIÓN -->
                                <?php if (!empty($evento["descripcion"])): ?>
                                    <div class="evento-descripcion">
                                        <i class="fa-solid fa-align-left"></i>
                                        <p>
                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $evento["descripcion"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                )
                                            ) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <!-- BOTONES -->
                                <div class="evento-acciones">

                                    <!-- EDITAR -->
                                    <a
                                        href="editar.php?id=<?= (int) $evento["id"] ?>"
                                        class="btn-editar"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                        Editar
                                    </a>

                                    <!-- ELIMINAR -->
                                    <form
                                        action="borrar.php"
                                        method="POST"
                                        onsubmit="return confirm('¿Quieres eliminar este evento?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $evento["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-eliminar"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </article>

                    <?php endforeach; ?>

                </section>

            <?php endif; ?>

        </div>

        <!-- FOOTER -->
        <footer>
            <i class="fa-solid fa-heart"></i>
            AgendaWeb · Organiza tu tiempo
        </footer>

    </main>

</body>
</html>
