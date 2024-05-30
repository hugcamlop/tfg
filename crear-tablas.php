<?php
// Conexión a la base de datos
$servidor = "localhost";
$usuarioBD = "hugcamlop";
$contrasenaBD = "kalblue1";
$base_datos = "Gamer";

$conn = new mysqli($servidor, $usuarioBD, $contrasenaBD, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear tabla usuarios con una columna adicional para imagen de perfil y biografía
$sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    imagen_perfil VARCHAR(255) DEFAULT NULL,
    biografia TEXT DEFAULT NULL
)";

// Crear tabla publicaciones con una columna adicional para contenido multimedia
$sql_publicaciones = "CREATE TABLE IF NOT EXISTS publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    fecha_publicacion DATETIME NOT NULL,
    id_usuario INT NOT NULL,
    multimedia VARCHAR(255) DEFAULT NULL,
    juego VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
)";

// Crear tabla comentarios con la columna parent_id para respuestas anidadas
$sql_comentarios = "CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    fecha_comentario DATETIME NOT NULL,
    id_usuario INT NOT NULL,
    id_publicacion INT NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comentarios(id) ON DELETE CASCADE
)";

// Reemplazar la tabla amigos con la nueva tabla followers
$sql_followers = "CREATE TABLE IF NOT EXISTS followers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL, -- El usuario que sigue
    followed_id INT NOT NULL, -- El usuario que es seguido
    fecha_seguido DATETIME NOT NULL, -- La fecha en que comenzaron a seguirse
    FOREIGN KEY (follower_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE (follower_id, followed_id) -- Para asegurar que solo puede seguir una vez
)";

// Crear tabla mensajes_privados con una columna adicional para contenido multimedia
$sql_mensajes_privados = "CREATE TABLE IF NOT EXISTS mensajes_privados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    fecha_envio DATETIME NOT NULL,
    id_remitente INT NOT NULL,
    id_destinatario INT NOT NULL,
    multimedia VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_remitente) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_destinatario) REFERENCES usuarios(id) ON DELETE CASCADE
)";

// Crear tabla juegos_usuario para almacenar los juegos, rangos y roles de los usuarios
$sql_juegos_usuario = "CREATE TABLE IF NOT EXISTS juegos_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    juego VARCHAR(50) NOT NULL,
    rango VARCHAR(50) DEFAULT NULL,
    rol VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
)";

// Ejecutar las consultas
$consultas = [$sql_usuarios, $sql_publicaciones, $sql_comentarios, $sql_followers, $sql_mensajes_privados, $sql_juegos_usuario];
foreach ($consultas as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Consulta ejecutada con éxito: $sql<br>";
    } else {
        echo "Error al ejecutar consulta: $conn->error <br>";
    }
}

// Cerrar la conexión
$conn->close();
?>
