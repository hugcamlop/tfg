<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: inicio.html");
    exit;
}

require_once 'conexion.php';
$userId = $_SESSION['idUsuario'];

// Obtener la lista de usuarios seguidos
$sql_seguidos = "SELECT u.id, u.usuario FROM followers f JOIN usuarios u ON f.followed_id = u.id WHERE f.follower_id = ?";
$stmt_seguidos = $conn->prepare($sql_seguidos);
$stmt_seguidos->bind_param("i", $userId);
$stmt_seguidos->execute();
$resultado_seguidos = $stmt_seguidos->get_result();
$usuarios_seguidos = $resultado_seguidos->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes Directos</title>
    <link rel="stylesheet" href="mensajes.css">
</head>
<body>

<div class="navbar">
    <a href="timeline.php">Inicio</a>
    <a href="juegos.php">Juegos</a>
    <a href="perfil.php">Perfil</a>
    <a href="mensajes.php">Mensajes</a>
    <a href="cerrar_sesion.php" style="float: right;">Cerrar Sesión</a>
</div>

<div class="contenedor-mensajes">
    <div class="lista-usuarios">
        <?php foreach ($usuarios_seguidos as $usuario): ?>
            <div class="usuario" onclick="seleccionarUsuario(<?php echo $usuario['id']; ?>)">
                <?php echo htmlspecialchars($usuario['usuario']); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="chat">
        <div class="mensajes" id="mensajes"></div>
        <form id="formMensaje">
            <textarea id="contenidoMensaje" name="contenido" placeholder="Escribe tu mensaje" required></textarea>
            <button type="submit">Enviar</button>
        </form>
    </div>
</div>

<script>
let usuarioSeleccionado = null;

function seleccionarUsuario(idUsuario) {
    usuarioSeleccionado = idUsuario;
    cargarMensajes(idUsuario);
}

document.getElementById('formMensaje').addEventListener('submit', function(event) {
    event.preventDefault();
    enviarMensaje();
});

function enviarMensaje() {
    if (!usuarioSeleccionado) {
        alert("Selecciona un usuario para enviar el mensaje.");
        return;
    }

    const datos = new FormData(document.getElementById('formMensaje'));
    datos.append('id_destinatario', usuarioSeleccionado);

    fetch('enviar_mensaje.php', {
        method: 'POST',
        body: datos
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('contenidoMensaje').value = '';
            cargarMensajes(usuarioSeleccionado);
        } else {
            alert(data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function cargarMensajes(idUsuario) {
    fetch('obtener_mensajes.php?id=' + idUsuario)
    .then(response => response.json())
    .then(mensajes => {
        const contenedor = document.getElementById('mensajes');
        contenedor.innerHTML = '';
        mensajes.forEach(mensaje => {
            const div = document.createElement('div');
            div.className = 'mensaje ' + (mensaje.id_remitente == <?php echo json_encode($userId); ?> ? 'propia' : 'ajena');
            div.innerHTML = `<p><strong>${mensaje.remitente}:</strong> ${mensaje.contenido}</p><span>${mensaje.fecha_envio}</span>`;
            contenedor.appendChild(div);
        });
        contenedor.scrollTop = contenedor.scrollHeight;
    })
    .catch(error => console.error('Error al cargar los mensajes:', error));
}
</script>

</body>
</html>
