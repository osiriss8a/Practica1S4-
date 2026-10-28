
<?php
require_once __DIR__ . '/partes/sesion.php';
require_once __DIR__ . '/conexion.php';

/* =========================
   VALIDAR MÉTODO POST
   ========================= */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: agenda.php");
    exit;
}

/* =========================
   OBTENER ID Y USUARIO
   ========================= */
$id = isset($_POST["id"])
    ? (int) $_POST["id"]
    : 0;

$usuarioId = (int) $_SESSION['uid'];

if ($id <= 0) {
    header("Location: agenda.php");
    exit;
}

/* =========================
   ELIMINAR SOLO EVENTOS PROPIOS
   ========================= */
$sql = "DELETE FROM eventos
        WHERE id = :id
          AND usuario_id = :usuario_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id' => $id,
    ':usuario_id' => $usuarioId
]);

/* =========================
   REGRESAR A LA AGENDA
   ========================= */
header("Location: agenda.php?eliminado=1");
exit;
?>
