<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit;
}

if (isset($_GET['id_publicacion'])) {
    $id_publicacion = intval($_GET['id_publicacion']);

    $sql = "SELECT c.*, u.usuario FROM comentarios c INNER JOIN usuarios u ON c.id_usuario = u.id WHERE c.id_publicacion = ? AND c.parent_id IS NULL ORDER BY c.fecha_comentario ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    $respuestas = [];
    while ($row = $result->fetch_assoc()) {
        $respuestas[] = $row;
    }

    echo json_encode($respuestas);
} elseif (isset($_GET['id_comentario'])) {
    $id_comentario = intval($_GET['id_comentario']);

    $sql = "SELECT c.*, u.usuario FROM comentarios c INNER JOIN usuarios u ON c.id_usuario = u.id WHERE c.parent_id = ? ORDER BY c.fecha_comentario ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_comentario);
    $stmt->execute();
    $result = $stmt->get_result();

    $respuestas = [];
    while ($row = $result->fetch_assoc()) {
        $respuestas[] = $row;
    }

    echo json_encode($respuestas);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de publicación o comentario no proporcionado.']);
}
?>
