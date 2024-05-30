<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => true, 'message' => 'Usuario no logueado.']);
    exit;
}

require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contenido'])) {
    $mensaje = trim($_POST['contenido']);
    $fecha_publicacion = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

    if (empty($mensaje)) {
        echo json_encode(['error' => true, 'message' => "El mensaje no puede estar vacío."]);
        exit;
    } else {
        $sql = "SELECT id FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['usuario']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $idUsuario = $fila['id'];

            // Incluir la fecha_publicacion en la inserción
            $query = "INSERT INTO publicaciones (contenido, fecha_publicacion, id_usuario) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $mensaje, $fecha_publicacion, $idUsuario);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => "Publicación creada con éxito."]);
            } else {
                echo json_encode(['error' => true, 'message' => "Error al crear la publicación."]);
            }
        } else {
            echo json_encode(['error' => true, 'message' => "Error al encontrar el usuario."]);
        }
    }
} else {
    echo json_encode(['error' => true, 'message' => "Solicitud no válida."]);
}
?>
