<?php
session_start();

// Verificar si se han enviado datos desde el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $email = $_POST["email"];
    $usuarioForm = $_POST["usuario"];
    $contrasenaForm = $_POST["contrasena"];

    // Validar los datos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($usuarioForm) || empty($contrasenaForm)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: registro.html");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "La dirección de correo electrónico no es válida.";
        header("Location: registro.html");
        exit;
    } else {
        $servidor = "localhost";
        $usuarioBD = "hugcamlop";
        $contrasenaBD = "kalblue1";
        $base_datos = "Gamer";

        $conn = new mysqli($servidor, $usuarioBD, $contrasenaBD, $base_datos);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        $contrasenaHash = password_hash($contrasenaForm, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, usuario, contrasena) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $apellido, $email, $usuarioForm, $contrasenaHash);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registro exitoso. ¡Bienvenido a Gamer!";

            $mensaje = "Hola $nombre $apellido,\n\nGracias por registrarte en Gamer. ¡Bienvenido!";
            $asunto = "Bienvenido a Gamer";
            $cabeceras = "From: tu_direccion_de_correo@example.com";

            if (!mail($email, $asunto, $mensaje, $cabeceras)) {
                $_SESSION['error'] = "Error al enviar el mensaje de bienvenida.";
            }
            header("Location: inicio.html");
            exit;
        } else {
            $_SESSION['error'] = "Error al registrar el usuario: " . $stmt->error;
            header("Location: registro.html");
            exit;
        }
    }
} else {
    header("Location: registro.html");
    exit();
}
?>
