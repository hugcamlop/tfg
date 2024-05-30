<?php
$servidor = "localhost";
$usuarioBD = "hugcamlop";
$contrasenaBD = "kalblue1";
$base_datos = "Gamer";

// Crear la conexión
$conn = new mysqli($servidor, $usuarioBD, $contrasenaBD, $base_datos);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
