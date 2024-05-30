<?php
session_start();
require_once 'conexion.php';

$id_usuario_actual = $_SESSION['idUsuario'];

$sql = "SELECT u.id, u.usuario FROM usuarios u
        INNER JOIN followers f ON u.id = f.followed_id
        WHERE f.follower_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

echo json_encode($usuarios);

$stmt->close();
$conn->close();
?>
