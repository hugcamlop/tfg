<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once 'conexion.php';

$idUsuarioLogueado = $_SESSION['idUsuario'];

// Se ha modificado la consulta SQL para incluir el campo 'imagen_perfil' de la tabla 'usuarios'.
$sql = "SELECT p.id, p.contenido, p.fecha_publicacion, u.usuario, u.id as id_usuario, u.imagen_perfil 
        FROM publicaciones p
        JOIN usuarios u ON p.id_usuario = u.id
        ORDER BY p.fecha_publicacion DESC";
$resultado = $conn->query($sql);

$publicaciones = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $fila['esPropietario'] = ($fila['id_usuario'] == $idUsuarioLogueado);
        // Asegúrate de que la ruta de la imagen de perfil sea correcta y accesible desde el cliente.
        // Si es necesario, ajusta la ruta de la imagen aquí.
        $publicaciones[] = $fila;
    }
}

echo json_encode($publicaciones);
?>
