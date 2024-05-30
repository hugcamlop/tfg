<?php
// Iniciar la sesión si aún no ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo a la página de inicio
if (!isset($_SESSION['idUsuario'])) {
    header("Location: inicio.html");
    exit;
}

require_once 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Buscar al usuario por ID
$sql_usuario = "SELECT * FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $_SESSION['idUsuario']);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();

if ($resultado_usuario->num_rows > 0) {
    $usuario = $resultado_usuario->fetch_assoc();
} else {
    echo "Usuario no encontrado. Por favor, verifique si está correctamente logueado.";
    exit; // O redirigir a una página de error o mensaje adecuado
}

// Recuperar publicaciones del usuario
$sql_publicaciones = "SELECT * FROM publicaciones WHERE id_usuario = ? ORDER BY fecha_publicacion DESC";
$stmt_publicaciones = $conn->prepare($sql_publicaciones);
$stmt_publicaciones->bind_param("i", $_SESSION['idUsuario']);
$stmt_publicaciones->execute();
$resultado_publicaciones = $stmt_publicaciones->get_result();

// Recuperar los juegos, rangos y roles del usuario
$sql_juegos = "SELECT * FROM juegos_usuario WHERE id_usuario = ?";
$stmt_juegos = $conn->prepare($sql_juegos);
$stmt_juegos->bind_param("i", $_SESSION['idUsuario']);
$stmt_juegos->execute();
$resultado_juegos = $stmt_juegos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>

<div class="navbar">
    <a href="timeline.php">Inicio</a>
    <a href="juegos.php">Juegos</a>
    <a href="perfil.php">Perfil</a>
    <a href="mensajes.php">Mensajes privados</a>
    <a href="cerrar_sesion.php" style="float: right;">Cerrar Sesión</a>
</div>

<div class="perfil">
    <h2>Bienvenido, <?php echo htmlspecialchars($usuario['usuario']); ?></h2>
    
    <!-- Mostrar imagen de perfil si existe -->
    <?php if (!empty($usuario['imagen_perfil'])): ?>
        <img src="<?php echo htmlspecialchars($usuario['imagen_perfil']); ?>" alt="Imagen de perfil" class="imagen-perfil">
    <?php endif; ?>
    
    <!-- Formulario para subir imagen de perfil -->
    <form action="subir_imagen_perfil.php" method="post" enctype="multipart/form-data">
        <h3>Subir/Actualizar Imagen de Perfil</h3>
        <input type="file" name="imagenPerfil" required>
        <button type="submit">Subir Imagen</button>
    </form>
    
    <!-- Mostrar biografía si existe -->
    <?php if (!empty($usuario['biografia'])): ?>
        <p class="bio"><?php echo nl2br(htmlspecialchars($usuario['biografia'])); ?></p>
    <?php endif; ?>

    <!-- Formulario para actualizar biografía -->
    <form action="actualizar_bio.php" method="post">
        <h3>Actualizar Biografía</h3>
        <textarea name="biografia" rows="5" placeholder="Escribe tu biografía aquí..."><?php echo htmlspecialchars($usuario['biografia']); ?></textarea>
        <button type="submit">Actualizar Biografía</button>
    </form>

    <!-- Formulario para establecer juego, rol y rango -->
    <form action="establecer_juego.php" method="post">
        <h3>Establecer Juego, Rol y Rango</h3>
        <label for="juego">Juego:</label>
        <select name="juego" id="juego" required>
            <option value="">Selecciona un juego</option>
            <option value="Valorant">Valorant</option>
            <option value="League of Legends">League of Legends</option>
            <option value="Apex Legends">Apex Legends</option>
        </select>
        
        <label for="rol">Rol:</label>
        <input type="text" name="rol" id="rol" placeholder="Escribe tu rol" required>
        
        <label for="rango">Rango:</label>
        <input type="text" name="rango" id="rango" placeholder="Escribe tu rango" required>
        
        <button type="submit">Guardar</button>
    </form>

    <!-- Mostrar los juegos, rangos y roles del usuario -->
    <div class="juegos-usuario">
        <h3>Mis Juegos</h3>
        <?php if ($resultado_juegos->num_rows > 0): ?>
            <?php while ($juego = $resultado_juegos->fetch_assoc()): ?>
                <div class="juego">
                    <p><strong>Juego:</strong> <?php echo htmlspecialchars($juego['juego']); ?></p>
                    <p><strong>Rol:</strong> <?php echo htmlspecialchars($juego['rol']); ?></p>
                    <p><strong>Rango:</strong> <?php echo htmlspecialchars($juego['rango']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No has añadido ningún juego aún.</p>
        <?php endif; ?>
    </div>

    <!-- Sección para mostrar las publicaciones del usuario -->
    <div class="publicaciones">
        <h3>Mis Publicaciones</h3>
        <?php if ($resultado_publicaciones->num_rows > 0): ?>
            <?php while ($publicacion = $resultado_publicaciones->fetch_assoc()): ?>
                <div class="publicacion">
                    <p><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
                    <span><?php echo htmlspecialchars($publicacion['fecha_publicacion']); ?></span>
                    <!-- Botón de borrado para cada publicación -->
                    <button onclick="confirmarBorrado(<?php echo $publicacion['id']; ?>)">Borrar</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No has hecho ninguna publicación aún.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarBorrado(idPublicacion) {
    const confirmacion = confirm("¿Estás seguro de que quieres borrar este mensaje?");
    if (confirmacion) {
        borrarPublicacion(idPublicacion);
    }
}

function borrarPublicacion(idPublicacion) {
    fetch('borrarPublicacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${idPublicacion}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Publicación borrada con éxito.");
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            alert(data.message || "Error al borrar la publicación.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
</script>

</body>
</html>
