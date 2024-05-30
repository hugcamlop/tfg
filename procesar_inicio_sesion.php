<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreUsuario = $_POST['usuario'];
    $contrasenaFormulario = $_POST['contrasena'];

    // Establecer los datos de conexión a la base de datos
    $servidor = "localhost";
    $usuarioBD = "hugcamlop";
    $contrasenaBD = "kalblue1";
    $base_datos = "Gamer";

    // Crear conexión a la base de datos
    $conn = new mysqli($servidor, $usuarioBD, $contrasenaBD, $base_datos);

    // Verificar si hay errores de conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Asegurar que la consulta a la base de datos sea segura frente a inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Iterar sobre cada resultado para verificar la contraseña
        while ($row = $result->fetch_assoc()) {
            if (password_verify($contrasenaFormulario, $row['contrasena'])) {
                // Si la contraseña es correcta, también almacena el ID del usuario en la sesión.
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['idUsuario'] = $row['id']; // Almacenamiento del ID del usuario en la sesión.
                header("Location: timeline.php");
                exit;
            }
        }
        // Si llega aquí, la contraseña no coincidió con ninguno de los usuarios encontrados
        header("Location: inicio.html?error=1");
        exit;
    } else {
        // Si no se encontró ningún usuario con ese nombre de usuario
        header("Location: inicio.html?error=1");
        exit;
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();
} else {
    header("Location: inicio.html");
    exit;
}
?>
