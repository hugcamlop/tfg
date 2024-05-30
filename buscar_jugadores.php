<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $juego = $_POST['juego'];
    $rango = $_POST['rango'];
    $rol = $_POST['rol'];

    $sql = "SELECT u.usuario, j.juego, j.rango, j.rol FROM juegos_usuario j INNER JOIN usuarios u ON j.id_usuario = u.id WHERE j.juego = ?";

    $params = [$juego];
    $types = 's';

    if (!empty($rango)) {
        $sql .= " AND j.rango LIKE ?";
        $params[] = "%" . $rango . "%";
        $types .= 's';
    }

    if (!empty($rol)) {
        $sql .= " AND j.rol LIKE ?";
        $params[] = "%" . $rol . "%";
        $types .= 's';
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $jugadores = [];
    while ($row = $result->fetch_assoc()) {
        $jugadores[] = $row;
    }

    if (count($jugadores) > 0) {
        echo json_encode(['success' => true, 'jugadores' => $jugadores]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron jugadores con esos criterios.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
