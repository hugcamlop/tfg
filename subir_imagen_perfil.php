<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo a la página de inicio
if (!isset($_SESSION['idUsuario'])) {
    header('Location: inicio.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['imagenPerfil']) && $_FILES['imagenPerfil']['error'] == 0) {
    $archivo = $_FILES['imagenPerfil'];
    $nombreOriginal = $archivo['name'];
    $fileExt = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $permitidos = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileExt, $permitidos)) {
        // Generar un nombre único para el archivo
        $nombreArchivo = uniqid('perfil_', true) . '.' . $fileExt;
        $rutaDestino = "uploads/" . $nombreArchivo;

        // Crear el directorio si no existe
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            // Actualizar la ruta de la imagen en la base de datos
            $sql = "UPDATE usuarios SET imagen_perfil = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $rutaDestino, $_SESSION['idUsuario']);
            if ($stmt->execute()) {
                header("Location: perfil.php"); // Redireccionar de vuelta a la página de perfil
                exit; // Asegúrate de llamar a exit después de header para evitar ejecuciones adicionales
            } else {
                echo "Error al guardar la imagen en la base de datos.";
            }
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "Formato de archivo no permitido. Solo se permiten imágenes JPG, JPEG, PNG, y GIF.";
    }
} else {
    echo "Error al subir el archivo. Asegúrate de seleccionar un archivo de imagen válido.";
}
?>
