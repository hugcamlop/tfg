<?php
require_once 'conexion.php';

if (isset($_GET['id_publicacion'])) {
    $idPublicacion = $_GET['id_publicacion'];

    $sql = "SELECT c.*, u.usuario FROM comentarios c JOIN usuarios u ON c.id_usuario = u.id WHERE c.id_publicacion = ? ORDER BY c.fecha_comentario ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPublicacion);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $respuestas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $respuestas[] = $fila;
    }

    echo json_encode($respuestas);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de publicación no proporcionado.']);
}
?>
