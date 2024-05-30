<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['idUsuario']) && isset($_POST['id_destinatario']) && isset($_POST['contenido'])) {
    $id_remitente = $_SESSION['idUsuario'];
    $id_destinatario = $_POST['id_destinatario'];
    $contenido = $_POST['contenido'];

    $sql = "INSERT INTO mensajes_privados (contenido, fecha_envio, id_remitente, id_destinatario) VALUES (?, NOW(), ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $contenido, $id_remitente, $id_destinatario);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o usuario no autenticado.']);
}

$conn->close();
?>
