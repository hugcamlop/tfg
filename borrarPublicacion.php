<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['idUsuario'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
        exit;
    }

    $idPublicacion = $_POST['id'];
    $idUsuario = $_SESSION['idUsuario'];

    // Verificar que la publicación pertenece al usuario actual
    $sql = "DELETE FROM publicaciones WHERE id = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idPublicacion, $idUsuario);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Publicación no encontrada o no pertenece al usuario.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la publicación.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
