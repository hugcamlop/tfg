<?php
// Inicia la sesión al principio del script
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo a la página de inicio
if (!isset($_SESSION['usuario'])) {
    header("Location: inicio.html");
    exit;
}

require_once 'conexion.php'; // Asegúrate de tener este archivo para conectar a tu base de datos

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos - Gamer Network</title>
    <link rel="stylesheet" href="juegos.css">
    <style>
        .navbar {
            background-color: #01579b;
            color: #FFFFFF;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar a {
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #0277BD;
        }

        .juegos-container {
            margin-top: 60px;
            padding: 20px;
        }

        .juego {
            margin-bottom: 20px;
        }

        .resultado {
            background-color: rgba(255, 255, 255, 0.8);
            color: #0277BD;
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #0277BD;
            border-radius: 8px;
        }

        .resultado p, .resultado span {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="timeline.php">Inicio</a>
    <a href="juegos.php">Juegos</a>
    <a href="perfil.php">Perfil</a>
    <a href="mensajes.php">Mensajes privados</a>
    <a href="cerrar_sesion.php" style="float: right;">Cerrar Sesión</a>
</div>

<div class="juegos-container">
    <h2>Buscar Jugadores</h2>
    <form id="formBusqueda">
        <label for="juego">Juego:</label>
        <select id="juego" name="juego">
            <option value="Valorant">Valorant</option>
            <option value="League of Legends">League of Legends</option>
            <option value="Apex Legends">Apex Legends</option>
        </select>

        <label for="rango">Rango:</label>
        <input type="text" id="rango" name="rango" placeholder="Introduce el rango">

        <label for="rol">Rol:</label>
        <input type="text" id="rol" name="rol" placeholder="Introduce el rol">

        <button type="submit">Buscar</button>
    </form>

    <div id="resultados">
        <!-- Los resultados de la búsqueda se mostrarán aquí -->
    </div>
</div>

<script>
document.getElementById('formBusqueda').addEventListener('submit', function(event) {
    event.preventDefault();
    buscarJugadores();
});

function buscarJugadores() {
    const juego = document.getElementById('juego').value;
    const rango = document.getElementById('rango').value;
    const rol = document.getElementById('rol').value;

    fetch('buscar_jugadores.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `juego=${juego}&rango=${rango}&rol=${rol}`
    })
    .then(response => response.json())
    .then(data => {
        const contenedorResultados = document.getElementById('resultados');
        contenedorResultados.innerHTML = '';
        if (data.success) {
            data.jugadores.forEach(jugador => {
                const div = document.createElement('div');
                div.className = 'resultado';
                div.innerHTML = `
                    <strong>${jugador.usuario}</strong>
                    <p>Juego: ${jugador.juego}</p>
                    <p>Rango: ${jugador.rango}</p>
                    <p>Rol: ${jugador.rol}</p>`;
                contenedorResultados.appendChild(div);
            });
        } else {
            contenedorResultados.innerHTML = `<p>${data.message}</p>`;
        }
    })
    .catch(error => console.error('Error al buscar jugadores:', error));
}
</script>

</body>
</html>
