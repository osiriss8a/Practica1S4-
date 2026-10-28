
<?php
require_once __DIR__ . '/partes/sesion.php';
require_once __DIR__ . '/conexion.php';

/* =========================
   VERIFICAR MÉTODO
   ========================= */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

/* =========================
   RECIBIR DATOS
   ========================= */
$titulo = trim($_POST["titulo"] ?? "");
$cliente = trim($_POST["cliente"] ?? "");
$fecha = $_POST["fecha"] ?? "";
$hora = $_POST["hora"] ?? "";
$categoriaId = (int) ($_POST["categoria_id"] ?? 0);
$ubicacion = trim($_POST["ubicacion"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

/* =========================
   VALIDAR CAMPOS
   ========================= */
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

/* =========================
   VALIDAR CATEGORÍA
   ========================= */
$stmtCategoria = $pdo->prepare(
    "SELECT id FROM categorias WHERE id = ?"
);
$stmtCategoria->execute([$categoriaId]);

if (!$stmtCategoria->fetch(PDO::FETCH_ASSOC)) {
    die("La categoría seleccionada no es válida.");
}

/* =========================
   OBTENER USUARIO ACTUAL
   ========================= */
$usuarioId = (int) $_SESSION['uid'];

/* =========================
   INSERTAR EVENTO
   ========================= */
$sql = "INSERT INTO eventos
        (titulo, cliente, fecha, hora, categoria_id,
         ubicacion, descripcion, usuario_id)
        VALUES
        (:titulo, :cliente, :fecha, :hora, :categoria_id,
         :ubicacion, :descripcion, :usuario_id)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":titulo" => $titulo,
    ":cliente" => $cliente,
    ":fecha" => $fecha,
    ":hora" => $hora,
    ":categoria_id" => $categoriaId,
    ":ubicacion" => $ubicacion,
    ":descripcion" => $descripcion,
    ":usuario_id" => $usuarioId
]);

/* =========================
   REGRESAR A MI AGENDA
   ========================= */
header("Location: agenda.php?guardado=1");
exit;
?>
