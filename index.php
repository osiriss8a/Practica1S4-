
<?php
require_once __DIR__ . '/partes/sesion.php';
require_once __DIR__ . '/conexion.php';

$resultado = $pdo->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
);

$categorias = $resultado->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/partes/sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendaWeb - Inicio</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
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
                <p>
                    Organiza tus momentos
                </p>
            </div>
        </div>

        <!-- MENÚ -->
        <nav class="menu">
            <a href="index.php" class="menu-item active">
                <i class="fa-solid fa-house"></i>
                <span>
                    Inicio
                </span>
            </a>

            <a href="agenda.php" class="menu-item">
                <i class="fa-solid fa-calendar-days"></i>
                <span>
                    Mi Agenda
                </span>
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

        <!-- PIE DEL SIDEBAR -->
        <div class="sidebar-footer">
            <i class="fa-solid fa-heart"></i>
            <span>
                AgendaWeb
            </span>
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
                        Bienvenido,
                        <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>

                <a href="logout.php" class="btn-guardar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Cerrar sesión
                </a>
            </header>


        <!-- ================= INICIO ================= -->
        <div class="inicio-contenido">
            <!-- PRESENTACIÓN -->
            <section class="inicio-presentacion">
                <div>
                    <p class="etiqueta">
                        <i class="fa-solid fa-sparkles"></i>
                        AGENDAWEB
                    </p>
                    <h2>
                        Organiza tus momentos
                    </h2>
                    <p class="texto-inicio">
                        Registra tus eventos, citas y actividades
                        para mantener todo organizado en un solo lugar.
                    </p>
                </div>

                <div class="decoracion-calendario">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </section>

            <!-- ================= FORMULARIO ================= -->
            <section class="formulario-card">
                <!-- TÍTULO DEL FORMULARIO -->
                <div class="formulario-titulo">
                    <div>
                        <h3>
                            Nuevo evento
                        </h3>
                        <p>
                            Completa la información de tu evento.
                        </p>
                    </div>
                    <i class="fa-solid fa-calendar-plus"></i>
                </div>

                <!-- FORMULARIO -->
                <form
                    class="formulario-agenda"
                    method="POST"
                    action="registrar.php"
                >
                    <!-- TÍTULO -->
                    <div class="campo">
                        <label for="titulo">
                            <i class="fa-solid fa-heading"></i>
                            Título del evento
                        </label>

                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            placeholder="Ej. Entrega de pastel"
                            maxlength="150"
                            required
                        >
                    </div>

                    <!-- CLIENTE -->
                    <div class="campo">
                        <label for="cliente">
                            <i class="fa-solid fa-user"></i>
                            Nombre del cliente
                        </label>

                        <input
                            type="text"
                            id="cliente"
                            name="cliente"
                            placeholder="Ej. María González"
                            maxlength="150"
                            required
                        >
                    </div>

                    <!-- FECHA -->
                    <div class="campo">
                        <label for="fecha">
                            <i class="fa-regular fa-calendar"></i>
                            Fecha
                        </label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            required
                        >
                    </div>

                    <!-- HORA -->
                    <div class="campo">
                        <label for="hora">
                            <i class="fa-regular fa-clock"></i>
                            Hora
                        </label>

                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            required
                        >
                    </div>

                    <!-- CATEGORÍA -->
                    <div class="campo">
                        <label for="categoria_id">
                            <i class="fa-solid fa-tag"></i>
                            Categoría
                        </label>

                        <select
                            id="categoria_id"
                            name="categoria_id"
                            required
                        >
                            <option value="">
                                Selecciona una categoría
                            </option>

                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>">
                                    <?= htmlspecialchars(
                                        $cat['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- UBICACIÓN -->
                    <div class="campo">
                        <label for="ubicacion">
                            <i class="fa-solid fa-location-dot"></i>
                            Ubicación
                        </label>

                        <input
                            type="text"
                            id="ubicacion"
                            name="ubicacion"
                            placeholder="Ej. Guadalajara, Jalisco"
                            maxlength="200"
                        >
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="campo campo-completo">
                        <label for="descripcion">
                            <i class="fa-solid fa-align-left"></i>
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            placeholder="Escribe los detalles del evento..."
                            rows="6"
                        ></textarea>
                    </div>

                    <!-- BOTONES -->
                    <div class="formulario-botones">
                        <!-- LIMPIAR -->
                        <button
                            type="reset"
                            class="btn-limpiar"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                            Limpiar
                        </button>

                        <!-- GUARDAR -->
                        <button
                            type="submit"
                            class="btn-guardar"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            Guardar evento
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- FOOTER -->
        <footer>
            <i class="fa-solid fa-heart"></i>
            AgendaWeb · Organiza tu tiempo
        </footer>
    </main>
</body>

</html>

