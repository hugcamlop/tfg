<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['idUsuario'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
        exit;
    }

    $follower_id = $_SESSION['idUsuario'];
    $followed_id = $_POST['followed_id'];

    // Verificar que no se está intentando seguir a sí mismo
    if ($follower_id == $followed_id) {
        echo json_encode(['success' => false, 'message' => 'No puedes seguirte a ti mismo.']);
        exit;
    }

    $sql = "INSERT INTO followers (follower_id, followed_id, fecha_seguido) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $follower_id, $followed_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al seguir al usuario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
