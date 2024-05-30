<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.html");
    exit;
}

require_once 'conexion.php';

if (!isset($_GET['id'])) {
    echo "ID de usuario no proporcionado.";
    exit;
}

$usuarioId = $_GET['id'];

// Obtener datos del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuarioId);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();

if ($resultado_usuario->num_rows > 0) {
    $usuario = $resultado_usuario->fetch_assoc();
} else {
    echo "Usuario no encontrado.";
    exit;
}

// Verificar si el usuario actual ya sigue a este usuario
$sql_seguido = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
$stmt_seguido = $conn->prepare($sql_seguido);
$stmt_seguido->bind_param("ii", $_SESSION['idUsuario'], $usuarioId);
$stmt_seguido->execute();
$resultado_seguido = $stmt_seguido->get_result();
$yaSeguido = $resultado_seguido->num_rows > 0;

// Obtener publicaciones del usuario
$sql_publicaciones = "SELECT * FROM publicaciones WHERE id_usuario = ? ORDER BY fecha_publicacion DESC";
$stmt_publicaciones = $conn->prepare($sql_publicaciones);
$stmt_publicaciones->bind_param("i", $usuarioId);
$stmt_publicaciones->execute();
$resultado_publicaciones = $stmt_publicaciones->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['usuario']); ?></title>
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
    <h2>Perfil de <?php echo htmlspecialchars($usuario['usuario']); ?></h2>
    
    <?php if (!empty($usuario['imagen_perfil'])): ?>
        <img src="<?php echo htmlspecialchars($usuario['imagen_perfil']); ?>" alt="Imagen de perfil" style="width: 150px; height: 150px;">
    <?php endif; ?>

    <!-- Mostrar "Siguiendo" o botón de seguir -->
    <div id="follow-container">
        <?php if ($yaSeguido): ?>
            <span id="follow-status">Siguiendo</span>
            <button id="unfollow-btn" onclick="toggleFollow(<?php echo $usuarioId; ?>, false)">Dejar de Seguir</button>
        <?php else: ?>
            <button id="follow-btn" onclick="toggleFollow(<?php echo $usuarioId; ?>, true)">Seguir</button>
        <?php endif; ?>
    </div>

    <!-- Sección para mostrar las publicaciones del usuario -->
    <div class="publicaciones">
        <h3>Publicaciones de <?php echo htmlspecialchars($usuario['usuario']); ?></h3>
        <?php if ($resultado_publicaciones->num_rows > 0): ?>
            <?php while ($publicacion = $resultado_publicaciones->fetch_assoc()): ?>
                <div class="publicacion">
                    <p><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
                    <span><?php echo htmlspecialchars($publicacion['fecha_publicacion']); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay publicaciones aún.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleFollow(followed_id, follow) {
    const action = follow ? 'follow_user.php' : 'unfollow_user.php';
    const followBtn = document.getElementById('follow-btn');
    const unfollowBtn = document.getElementById('unfollow-btn');
    const followStatus = document.getElementById('follow-status');
    
    fetch(action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `followed_id=${followed_id}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if (follow) {
                if (followBtn) followBtn.style.display = 'none';
                if (unfollowBtn) unfollowBtn.style.display = 'inline';
                followStatus.textContent = 'Siguiendo';
            } else {
                if (followBtn) followBtn.style.display = 'inline';
                if (unfollowBtn) unfollowBtn.style.display = 'none';
                followStatus.textContent = '';
            }
        } else {
            alert(data.message || "Error al cambiar el estado de seguimiento.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
</script>

</body>
</html>
