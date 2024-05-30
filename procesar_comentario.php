<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contenido = $_POST['contenido'];
    $id_publicacion = $_POST['id_publicacion'];
    $id_usuario = $_SESSION['idUsuario'];
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;

    $sql = "INSERT INTO comentarios (contenido, fecha_comentario, id_usuario, id_publicacion, parent_id) VALUES (?, NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $contenido, $id_usuario, $id_publicacion, $parent_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
