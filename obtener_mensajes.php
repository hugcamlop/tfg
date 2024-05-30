<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit;
}

$userId = $_SESSION['idUsuario'];
$idUsuario = $_GET['id'];

// Obtener los mensajes entre los usuarios
$sql_mensajes = "
    SELECT m.*, u.usuario AS remitente
    FROM mensajes_privados m
    JOIN usuarios u ON m.id_remitente = u.id
    WHERE (m.id_remitente = ? AND m.id_destinatario = ?) 
       OR (m.id_remitente = ? AND m.id_destinatario = ?)
    ORDER BY m.fecha_envio ASC
";

$stmt_mensajes = $conn->prepare($sql_mensajes);
$stmt_mensajes->bind_param("iiii", $userId, $idUsuario, $idUsuario, $userId);
$stmt_mensajes->execute();
$resultado_mensajes = $stmt_mensajes->get_result();
$mensajes = $resultado_mensajes->fetch_all(MYSQLI_ASSOC);

echo json_encode($mensajes);
?>
