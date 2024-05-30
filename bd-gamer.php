<?php
$servidor = "localhost";
$usuarioBD = "hugcamlop";
$contrasenaBD = "kalblue1";

// Crear la conexion
$conn = new mysqli($servidor, $usuarioBD, $contrasenaBD);
// Comprobar la conexion
if ($conn->connect_error) {
  die("Conexion fallida: " . $conn->connect_error);
}

// Crear la base de datos Gamer
$sql = "CREATE DATABASE Gamer";
if ($conn->query($sql) === TRUE) {
  echo "Base de datos creada correctamente";
} else {
  echo "Error creando la base de datos: " . $conn->error;
}

$conn->close();
?>