<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.html");
    exit;
}

require_once 'conexion.php';
$userId = $_SESSION['idUsuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Timeline</title>
    <link rel="stylesheet" href="timeline.css">
</head>
<body>

<div class="navbar">
    <a href="timeline.php">Inicio</a>
    <a href="juegos.php">Juegos</a>
    <a href="perfil.php">Perfil</a>
    <a href="mensajes.php">Mensajes privados</a>
    <a href="cerrar_sesion.php" style="float: right;">Cerrar Sesión</a>
</div>

<div class="publicar-tweet">
    <h3>Publicar Nuevo Mensaje</h3>
    <form id="formPublicacion">
        <label for="mensaje">Tu mensaje:</label>
        <textarea id="contenido" name="contenido" placeholder="Escribe tu nueva publicación aquí" required></textarea>
        <button type="button" onclick="mostrarPanelEmojis()">😊</button>
        <div id="panelEmojis" style="display:none;"></div>
        <button type="submit">Publicar</button>
    </form>
</div>

<div class="timeline" id="timeline">
    <!-- Las publicaciones se cargarán aquí -->
</div>

<script>
const userId = <?php echo json_encode($userId); ?>;

document.getElementById('formPublicacion').addEventListener('submit', function(event) {
    event.preventDefault();
    enviarPublicacion();
});

function enviarPublicacion() {
    const datos = new FormData(document.getElementById('formPublicacion'));

    fetch('procesar_publicacion.php', {
        method: 'POST',
        body: datos
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarPublicaciones();
            document.getElementById('contenido').value = '';
        } else {
            alert(data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function mostrarPanelEmojis() {
    const panel = document.getElementById('panelEmojis');
    if (panel.innerHTML === '') {
        const emojis = ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊'];
        emojis.forEach(emoji => {
            const btn = document.createElement('button');
            btn.textContent = emoji;
            btn.onclick = function() { seleccionarEmoji(emoji); };
            panel.appendChild(btn);
        });
    }
    panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
}

function seleccionarEmoji(emoji) {
    const texto = document.getElementById('contenido');
    texto.value += emoji;
}

document.addEventListener('DOMContentLoaded', cargarPublicaciones);

function cargarPublicaciones() {
    fetch('obtener_publicaciones.php')
    .then(response => response.json())
    .then(publicaciones => {
        const contenedor = document.getElementById('timeline');
        contenedor.innerHTML = '';
        publicaciones.forEach(publicacion => {
            const div = document.createElement('div');
            div.className = 'publicacion';
            let botonBorrar = '';
            let botonSeguir = '';
            let seguirTexto = '';

            if (Number(publicacion.id_usuario) !== Number(userId)) {
                botonSeguir = `<button onclick="toggleFollow(${publicacion.id_usuario})" id="follow-btn-${publicacion.id_usuario}" style="float: right;">Seguir</button>`;
            }

            if (publicacion.seguido) {
                seguirTexto = '<span class="siguiendo">Siguiendo</span>';
                botonSeguir = `<button onclick="toggleFollow(${publicacion.id_usuario})" id="follow-btn-${publicacion.id_usuario}" style="float: right;">Dejar de Seguir</button>`;
            }

            if (Number(publicacion.id_usuario) === Number(userId)) {
                botonBorrar = `<button onclick="confirmarBorrado(${publicacion.id})" style="float: right;">Borrar</button>`;
            }

            div.innerHTML = `
                <strong>Publicado por: <a href="perfil_usuario.php?id=${publicacion.id_usuario}">${publicacion.usuario}</a></strong>
                <p>${publicacion.contenido}</p>
                <span>${publicacion.fecha_publicacion}</span>
                ${botonBorrar}
                ${botonSeguir}
                ${seguirTexto}
                <div class="responder">
                    <textarea placeholder="Responder a esta publicación..." class="respuesta-textarea"></textarea>
                    <button onclick="enviarRespuesta(${publicacion.id}, this.previousElementSibling)">Responder</button>
                </div>
                <div class="respuestas" id="respuestas-${publicacion.id}">
                    <!-- Respuestas se cargarán aquí -->
                </div>`;
            contenedor.appendChild(div);

            cargarRespuestas(publicacion.id);
        });
    })
    .catch(error => console.error('Error al cargar las publicaciones:', error));
}

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
        if(data.success) {
            alert("Publicación borrada con éxito.");
            cargarPublicaciones();
        } else {
            alert("Error al borrar la publicación.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function toggleFollow(followed_id) {
    const followBtn = document.getElementById(`follow-btn-${followed_id}`);
    const isFollowing = followBtn.textContent === 'Seguir' ? false : true;
    const action = isFollowing ? 'unfollow_user.php' : 'follow_user.php';

    fetch(action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `followed_id=${followed_id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            followBtn.textContent = isFollowing ? 'Seguir' : 'Dejar de Seguir';
        } else {
            alert(data.message || "Error al cambiar el estado de seguimiento.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function enviarRespuesta(idPublicacion, textarea) {
    const contenido = textarea.value.trim();
    if (contenido === '') {
        alert("La respuesta no puede estar vacía.");
        return;
    }

    fetch('enviar_respuesta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_publicacion=${idPublicacion}&contenido=${contenido}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            cargarRespuestas(idPublicacion);
        } else {
            alert(data.message || "Error al enviar la respuesta.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function cargarRespuestas(idPublicacion) {
    fetch(`obtener_respuestas.php?id_publicacion=${idPublicacion}`)
    .then(response => response.json())
    .then(respuestas => {
        const contenedorRespuestas = document.getElementById(`respuestas-${idPublicacion}`);
        contenedorRespuestas.innerHTML = '';
        respuestas.forEach(respuesta => {
            const divRespuesta = document.createElement('div');
            divRespuesta.className = 'respuesta';
            divRespuesta.innerHTML = `
                <strong>${respuesta.usuario}</strong>
                <p>${respuesta.contenido}</p>
                <span>${respuesta.fecha_comentario}</span>
                <button onclick="mostrarFormularioRespuesta(${respuesta.id}, ${idPublicacion})">Responder</button>
                <div id="respuestas-${respuesta.id}">
                    <!-- Respuestas anidadas se cargarán aquí -->
                </div>
                <form id="form-respuesta-${respuesta.id}" class="form-respuesta" onsubmit="return enviarRespuestaAnidada(${respuesta.id}, ${idPublicacion}, this)" style="display:none;">
                    <textarea name="respuesta" placeholder="Escribe una respuesta"></textarea>
                    <button type="submit">Responder</button>
                </form>`;
            contenedorRespuestas.appendChild(divRespuesta);
            cargarRespuestasAnidadas(respuesta.id, idPublicacion);
        });
    })
    .catch(error => console.error('Error al cargar las respuestas:', error));
}
function cargarRespuestasAnidadas(idComentario, idPublicacion) {
    fetch(`obtener_respuestas.php?id_comentario=${idComentario}`)
    .then(response => response.json())
    .then(respuestas => {
        const contenedorRespuestas = document.getElementById(`respuestas-${idComentario}`);
        contenedorRespuestas.innerHTML = '';
        respuestas.forEach(respuesta => {
            const divRespuesta = document.createElement('div');
            divRespuesta.className = 'respuesta';
            divRespuesta.innerHTML = `
                <strong>${respuesta.usuario}</strong>
                <p>${respuesta.contenido}</p>
                <span>${respuesta.fecha_comentario}</span>
                <button onclick="mostrarFormularioRespuesta(${respuesta.id}, ${idPublicacion})">Responder</button>
                <div id="respuestas-${respuesta.id}">
                    <!-- Respuestas anidadas se cargarán aquí -->
                </div>
                <form id="form-respuesta-${respuesta.id}" class="form-respuesta" onsubmit="return enviarRespuestaAnidada(${respuesta.id}, ${idPublicacion}, this)" style="display:none;">
                    <textarea name="respuesta" placeholder="Escribe una respuesta"></textarea>
                    <button type="submit">Responder</button>
                </form>`;
            contenedorRespuestas.appendChild(divRespuesta);
            cargarRespuestasAnidadas(respuesta.id, idPublicacion);
        });
    })
    .catch(error => console.error('Error al cargar las respuestas anidadas:', error));
}

function mostrarFormularioRespuesta(idComentario, idPublicacion) {
    const formulario = document.getElementById(`form-respuesta-${idComentario}`);
    formulario.style.display = 'block';
}

function enviarRespuestaAnidada(idComentario, idPublicacion, form) {
    const contenido = form.querySelector('textarea[name="respuesta"]').value.trim();
    if (contenido === '') {
        alert("La respuesta no puede estar vacía.");
        return false;
    }

    fetch('enviar_respuesta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_publicacion=${idPublicacion}&contenido=${contenido}&parent_id=${idComentario}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.querySelector('textarea[name="respuesta"]').value = '';
            cargarRespuestasAnidadas(idComentario, idPublicacion);
        } else {
            alert(data.message || "Error al enviar la respuesta.");
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
    return false;
}
</script>

</body>
</html>
