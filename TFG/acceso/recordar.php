<?php
require_once("../funciones.php");
require_once("../variables.php");

$error_message = "";   // Variable para almacenar mensajes de error
$success_message = ""; // Variable para almacenar mensajes de éxito

// Se ejecuta solo si el formulario se envía por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Captura el email enviado por el usuario

    try {
        $conexion = conectarPDO($host, $user, $password, $bbdd); // Conexión a la base de datos

        // Verificar si el correo electrónico existe en la tabla usuarios
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Generar un token único para restablecimiento de contraseña
            $token = bin2hex(random_bytes(16));

            // Guardar el token en la base de datos para este usuario
            $query = "UPDATE usuarios SET token = :token WHERE email = :email";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Crear carpeta 'emails' si no existe
            if (!file_exists('emails')) {
                mkdir('emails', 0777, true);
            }

            // Preparar el "correo" de restablecimiento
            $resetLink = "http://localhost/TFG/acceso/restablecer.php?token=$token";
            $subject = "Restablecimiento de contraseña";
            $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $resetLink";
            $headers = "From: no-reply@tu-dominio.com";

            // Guardar el "correo" en un archivo dentro de la carpeta emails (simulación de envío)
            file_put_contents("emails/$email.txt", "$subject\n\n$message");

            // Mensaje de éxito que se mostrará al usuario
            $success_message = "Se ha enviado un correo para restablecer tu contraseña.";
        } else {
            // Si el correo no está registrado, mostrar mensaje de error
            $error_message = "El correo electrónico no está registrado.";
        }
    } catch (PDOException $e) {
        // Captura errores de conexión con la base de datos
        $error_message = "Error de conexión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordar Contraseña</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(45deg, #0066cc, #00b3b3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }

        .reset-container {
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .reset-container h2 {
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: #333;
        }

        .reset-container input,
        .reset-container button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
            outline: none;
        }

        .reset-container input:focus,
        .reset-container button:focus {
            border-color: #00b3b3;
        }

        .reset-container input {
            background-color: #f7f7f7;
        }

        .reset-container button {
            background-color: #0066cc;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reset-container button:hover {
            background-color: #005bb5;
        }

        .reset-container p {
            margin-top: 1rem;
            color: #666;
        }

        .reset-container p a {
            color: #0066cc;
            text-decoration: none;
        }

        .reset-container p a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4d4d;
            background-color: #ffe6e6;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            text-align: center;
            border: 1px solid #ffcccc;
        }

        .success-message {
            color: #4CAF50;
            background-color: #e8f5e9;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            text-align: center;
            border: 1px solid #a5d6a7;
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <h2>Recordar Contraseña</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito si existe -->
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="recordar.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <button type="submit">Enviar</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
</body>

</html>