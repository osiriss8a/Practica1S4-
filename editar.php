
<?php
require_once __DIR__ . '/partes/sesion.php';
require_once __DIR__ . '/conexion.php';

/* =========================
   OBTENER USUARIO E ID
   ========================= */
$usuarioId = (int) $_SESSION['uid'];

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header("Location: agenda.php");
    exit;
}

/* =========================
   BUSCAR EVENTO PROPIO
   ========================= */
$sql = "SELECT
            eventos.id,
            eventos.titulo,
            eventos.cliente,
            eventos.fecha,
            eventos.hora,
            eventos.categoria_id,
            categorias.nombre AS categoria,
            eventos.ubicacion,
            eventos.descripcion
        FROM eventos
        INNER JOIN categorias
            ON eventos.categoria_id = categorias.id
        WHERE eventos.id = :id
          AND eventos.usuario_id = :usuario_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':usuario_id' => $usuarioId
]);

$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    http_response_code(404);
    exit("El evento no existe o no tienes permiso para editarlo.");
}

/* =========================
   ACTUALIZAR EVENTO
   ========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $cliente = trim($_POST["cliente"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $hora = $_POST["hora"] ?? "";
    $categoriaId = (int) ($_POST["categoria_id"] ?? 0);
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    /* VALIDACIONES */
    if ($titulo === "") {
        die("El título es obligatorio.");
    }

    if ($cliente === "") {
        die("El nombre del cliente es obligatorio.");
    }

    if ($fecha === "") {
        die("La fecha es obligatoria.");
    }

    if ($hora === "") {
        die("La hora es obligatoria.");
    }

    /* VALIDAR CATEGORÍA */
    $stmtCategoria = $pdo->prepare(
        "SELECT COUNT(*) FROM categorias WHERE id = :id"
    );

    $stmtCategoria->execute([
        ':id' => $categoriaId
    ]);

    if (!$stmtCategoria->fetchColumn()) {
        die("La categoría seleccionada no es válida.");
    }

    /* ACTUALIZAR SOLO SI PERTENECE AL USUARIO */
    $sql = "UPDATE eventos
            SET titulo = :titulo,
                cliente = :cliente,
                fecha = :fecha,
                hora = :hora,
                categoria_id = :categoria_id,
                ubicacion = :ubicacion,
                descripcion = :descripcion
            WHERE id = :id
              AND usuario_id = :usuario_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':titulo' => $titulo,
        ':cliente' => $cliente,
        ':fecha' => $fecha,
        ':hora' => $hora,
        ':categoria_id' => $categoriaId,
        ':ubicacion' => $ubicacion,
        ':descripcion' => $descripcion,
        ':id' => $id,
        ':usuario_id' => $usuarioId
    ]);

    header("Location: agenda.php?actualizado=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Editar evento - AgendaWeb</title>

    <!-- GOOGLE FONTS -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS -->
    <link
        rel="stylesheet"
        href="css/styles.css"
    >
    <style>
        /* =====================================================
           EDITAR EVENTO
           ===================================================== */
        .editar-pagina {
            min-height: calc(100vh - 80px);
            padding: 45px 60px 60px;
            background: #FFF9FA;
        }

        .editar-contenido {
            max-width: 900px;
            margin: 0 auto;
        }

        /* =========================
           ENCABEZADO
           ========================= */
        .editar-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
            margin-bottom: 35px;
        }

        .editar-titulo {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .editar-icono {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F8E8EC;
            color: #7A2636;
            border-radius: 14px;
            font-size: 20px;
        }

        .editar-titulo-texto p {
            margin: 0 0 5px;
            font-family: "Inter", sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #9B3F52;
        }

        .editar-titulo-texto h2 {
            margin: 0;
            font-family: "Playfair Display", serif;
            font-size: 34px;
            font-weight: 700;
            color: #5A1824;
        }

        .editar-regresar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 38px;
            padding: 0 16px;
            border: 1px solid #E2CDD2;
            border-radius: 9px;
            background: transparent;
            color: #7A2636;
            text-decoration: none;
            font-family: "Inter", sans-serif;
            font-size: 13px;
            font-weight: 600;
            transition: .2s ease;
        }

        .editar-regresar:hover {
            background: #F8E8EC;
            border-color: #E8B7C0;
        }

        /* =========================
           FORMULARIO
           ========================= */
        .editar-formulario {
            width: 100%;
        }

        .editar-campos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px 25px;
        }

        .editar-campo {
            display: flex;
            flex-direction: column;
        }

        .editar-campo-completo {
            grid-column: 1 / -1;
        }

        .editar-campo label {
            margin-bottom: 8px;
            font-family: "Inter", sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #3F3034;
        }

        .editar-campo label i {
            margin-right: 7px;
            color: #9B3F52;
        }

        .editar-campo input,
        .editar-campo textarea,
        .editar-campo select {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #E2CDD2;
            border-radius: 10px;
            background: #FFFFFF;
            color: #3F3034;
            font-family: "Inter", sans-serif;
            font-size: 14px;
            outline: none;
            transition: .2s ease;
        }

        .editar-campo input,
        .editar-campo select {
            height: 45px;
            padding: 0 14px;
        }

        .editar-campo textarea {
            min-height: 130px;
            padding: 13px 14px;
            resize: vertical;
            line-height: 1.5;
        }

        .editar-campo input:focus,
        .editar-campo textarea:focus,
        .editar-campo select:focus {
            border-color: #9B3F52;
            box-shadow: 0 0 0 3px rgba(232, 183, 192, .25);
        }

        .editar-campo input:hover,
        .editar-campo textarea:hover,
        .editar-campo select:hover {
            border-color: #D2BFC4;
        }

        /* =========================
           INFORMACIÓN
           ========================= */
        .editar-info {
            margin-top: 28px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 3px solid #E8B7C0;
            background: #F8E8EC;
            color: #76666B;
            font-family: "Inter", sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .editar-info i {
            color: #7A2636;
            font-size: 15px;
        }

        /* =========================
           BOTONES
          ========================= */
        .editar-acciones {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .editar-btn-cancelar,
        .editar-btn-guardar {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 17px;
            border-radius: 9px;
            font-family: "Inter", sans-serif;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .editar-btn-cancelar {
            border: 1px solid #E2CDD2;
            background: #F8E8EC;
            color: #7A2636;

        }



        .editar-btn-cancelar:hover {

            background: #F1DCE1;

        }



        .editar-btn-guardar {

            border: 1px solid #7A2636;

            background: #7A2636;

            color: #FFFFFF;

        }



        .editar-btn-guardar:hover {

            background: #5A1824;

            border-color: #5A1824;

        }



        /* =========================
           RESPONSIVE
           ========================= */

        @media (max-width: 750px) {

            .editar-pagina {

                padding: 30px 25px 45px;

            }



            .editar-encabezado {

                align-items: flex-start;

                flex-direction: column;

            }



            .editar-campos {

                grid-template-columns: 1fr;

            }



            .editar-campo-completo {

                grid-column: auto;

            }



            .editar-titulo-texto h2 {

                font-size: 29px;

            }



            .editar-regresar {

                width: fit-content;

            }

        }



        @media (max-width: 500px) {

            .editar-pagina {

                padding: 25px 18px 40px;

            }



            .editar-titulo {

                gap: 12px;

            }



            .editar-icono {

                width: 45px;

                height: 45px;

            }



            .editar-titulo-texto h2 {

                font-size: 26px;

            }



            .editar-acciones {

                justify-content: stretch;

            }



            .editar-btn-cancelar,

            .editar-btn-guardar {

                flex: 1;

            }

        }

    </style>

</head>



<body>


    <!-- EFECTOS DE FONDO -->

    <div class="glow glow-1"></div>

    <div class="glow glow-2"></div>



    <!-- =========================
         CONTENIDO
         ========================= -->

    <main class="editar-pagina">


        <div class="editar-contenido">


            <!-- ENCABEZADO -->

            <header class="editar-encabezado">


                <div class="editar-titulo">


                    <div class="editar-icono">

                        <i class="fa-solid fa-pen"></i>

                    </div>


                    <div class="editar-titulo-texto">


                        <p>

                            <i class="fa-solid fa-calendar-check"></i>

                            MODIFICAR EVENTO

                        </p>


                        <h2>
                            Editar evento
                        </h2>


                    </div>


                </div>



                <a
                    href="agenda.php"
                    class="editar-regresar"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Mi agenda

                </a>


            </header>



            <!-- =========================
                 FORMULARIO
                 ========================= -->

            <form
                action="editar.php?id=<?= $evento["id"] ?>"
                method="POST"
                class="editar-formulario"
            >


                <div class="editar-campos">


                    <!-- TÍTULO -->

                    <div class="editar-campo editar-campo-completo">


                        <label for="titulo">

                            <i class="fa-solid fa-heading"></i>

                            Título del evento

                        </label>


                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            maxlength="150"
                            value="<?= htmlspecialchars(
                                $evento["titulo"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                            placeholder="Escribe el título del evento"
                            required
                        >


                    </div>



                    <!-- CLIENTE -->

                    <div class="editar-campo">


                        <label for="cliente">

                            <i class="fa-solid fa-user"></i>

                            Nombre del cliente

                        </label>


                        <input
                            type="text"
                            id="cliente"
                            name="cliente"
                            maxlength="150"
                            value="<?= htmlspecialchars(
                                $evento["cliente"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                            placeholder="Ej. María González"
                            required
                        >


                    </div>



                    <!-- ID -->

                    <div class="editar-campo">


                        <label>

                            <i class="fa-solid fa-hashtag"></i>

                            Evento

                        </label>


                        <input
                            type="text"
                            value="#<?= $evento["id"] ?>"
                            readonly
                        >


                    </div>



                    <!-- FECHA -->

                    <div class="editar-campo">


                        <label for="fecha">

                            <i class="fa-regular fa-calendar"></i>

                            Fecha

                        </label>


                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            value="<?= htmlspecialchars(
                                $evento["fecha"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                            required
                        >


                    </div>



                    <!-- HORA -->

                    <div class="editar-campo">


                        <label for="hora">

                            <i class="fa-regular fa-clock"></i>

                            Hora

                        </label>


                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            value="<?= htmlspecialchars(
                                $evento["hora"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                            required
                        >


                    </div>



                    <!-- CATEGORÍA -->
                    <div class="editar-campo">
                        <label for="categoria_id">
                            <i class="fa-solid fa-tag"></i>
                            Categoría
                        </label>

                        <?php
                        $stmtCategorias = $pdo->query(
                            "SELECT id, nombre FROM categorias ORDER BY nombre"
                        );
                        $categorias = $stmtCategorias->fetchAll();
                        ?>

                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecciona una categoría</option>

                            <?php foreach ($categorias as $cat): ?>
                                <option
                                    value="<?= (int) $cat['id'] ?>"
                                    <?= (int) $evento['categoria_id'] === (int) $cat['id']
                                        ? 'selected'
                                        : '' ?>
                                >
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
                    <div class="editar-campo">
                        <label for="ubicacion">
                            <i class="fa-solid fa-location-dot"></i>
                            Ubicación
                        </label>

                        <input
                            type="text"
                            id="ubicacion"
                            name="ubicacion"
                            maxlength="200"
                            value="<?= htmlspecialchars(
                                $evento["ubicacion"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                            placeholder="Ej. Guadalajara, Jalisco"
                        >
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="editar-campo editar-campo-completo">
                        <label for="descripcion">
                            <i class="fa-solid fa-align-left"></i>
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="6"
                            placeholder="Agrega una descripción para este evento..."
                        ><?= htmlspecialchars(
                            $evento["descripcion"] ?? "",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?></textarea>
                    </div>
                </div>

                <!-- =========================
                     INFORMACIÓN
                     ========================= -->
                <div class="editar-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>
                        Modifica los datos que necesites y después selecciona
                        <strong>Guardar cambios</strong>
                        para actualizar el evento.
                    </span>
                </div>

                <!-- =========================
                     BOTONES
                     ========================= -->
                <div class="editar-acciones">
                    <a
                        href="agenda.php"
                        class="editar-btn-cancelar"
                    >
                        <i class="fa-solid fa-xmark"></i>
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="editar-btn-guardar"
                    >
                        <i class="fa-solid fa-check"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
