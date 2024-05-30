<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['juego']) && isset($_POST['rango']) && isset($_POST['rol'])) {
    $juego = $_POST['juego'];
    $rango = $_POST['rango'];
    $rol = $_POST['rol'];
    $idUsuario = $_SESSION['idUsuario'];

    // Verificar si el usuario ya tiene un juego configurado
    $sql_verificar = "SELECT * FROM juegos_usuario WHERE id_usuario = ? AND juego = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("is", $idUsuario, $juego);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows > 0) {
        // Actualizar el juego existente
        $sql_actualizar = "UPDATE juegos_usuario SET rango = ?, rol = ? WHERE id_usuario = ? AND juego = ?";
        $stmt_actualizar = $conn->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("ssis", $rango, $rol, $idUsuario, $juego);
        if ($stmt_actualizar->execute()) {
            header("Location: perfil.php");
            exit;
        } else {
            echo "Error al actualizar el juego.";
        }
    } else {
        // Insertar un nuevo juego
        $sql_insertar = "INSERT INTO juegos_usuario (id_usuario, juego, rango, rol) VALUES (?, ?, ?, ?)";
        $stmt_insertar = $conn->prepare($sql_insertar);
        $stmt_insertar->bind_param("isss", $idUsuario, $juego, $rango, $rol);
        if ($stmt_insertar->execute()) {
            header("Location: perfil.php");
            exit;
        } else {
            echo "Error al insertar el juego.";
        }
    }
} else {
    echo "Solicitud no válida.";
}
?>
