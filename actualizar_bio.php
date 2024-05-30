<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    header('Location: inicio.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    $usuarioId = $_SESSION['idUsuario'];

    $sql = "UPDATE usuarios SET bio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $bio, $usuarioId);

    if ($stmt->execute()) {
        header("Location: perfil.php");
        exit;
    } else {
        echo "Error al actualizar la biografía.";
    }
} else {
    header("Location: perfil.php");
    exit;
}
?>
