<?php
session_start();
require_once("../funciones.php");
require_once("../variables.php");

try {
    // Conexión a la base de datos usando PDO
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Solo se procesa si el formulario se envía por POST
        $nombre = $_POST['nombre']; // Nombre del usuario
        $email = $_POST['email']; // Correo electrónico del usuario
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashear la contraseña
        $perfil_id = $_POST['perfil_id']; // Perfil del usuario: Demandante (4) u Ofertante (3)
        $token = bin2hex(random_bytes(16)); // Generar token único para verificación de correo
        $created_at = date('Y-m-d H:i:s'); // Fecha y hora de creación
        $updated_at = date('Y-m-d H:i:s'); // Fecha y hora de actualización

        // Comprobar si el usuario ya existe en la base de datos
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarioExistente) {
            // Si el usuario ya existe, se muestra un error
            $error = "El usuario ya existe.";
        } else {
            // Insertar el nuevo usuario con estado activo = 0 (inactivo hasta verificar)
            $query = "INSERT INTO usuarios (nombre, email, password, perfil_id, activo, token, created_at, updated_at) 
                      VALUES (:nombre, :email, :password, :perfil_id, 0, :token, :created_at, :updated_at)";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
            $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Crear carpeta 'emails' si no existe
                if (!file_exists('emails')) {
                    mkdir('emails', 0777, true);
                }

                // Preparar correo de verificación (simulado guardando en archivo)
                $verificationLink = "http://localhost/TFG/acceso/validar.php?token=$token";
                $subject = "Verificación de cuenta";
                $message = "Haz clic en el siguiente enlace para verificar tu correo electrónico: $verificationLink";
                $headers = "From: no-reply@tu-dominio.com";

                // Guardar el "correo" en un archivo dentro de la carpeta emails
                file_put_contents("emails/$email.txt", "Subject: $subject\n\n$message");

                // Mostrar mensaje de éxito en pantalla con animación fade-out y redirección automática
                echo "<div class='success-message'>
                        <h3>Registro exitoso.</h3>
                        <p>Revisa tu correo electrónico para verificar tu cuenta.</p>
                      </div>";
                echo "<script>
                        setTimeout(function() {
                            document.querySelector('.success-message').classList.add('fade-out');
                        }, 4500);
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 5000);
                      </script>";
                exit();
            } else {
                // Error al insertar usuario en la base de datos
                $error = "Error al registrar el usuario.";
            }
        }
    }
} catch (PDOException $e) {
    // Captura errores de conexión con la base de datos
    echo "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeIn 1s forwards;
        }

        .register-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }

        .register-container form {
            text-align: center;
        }

        .register-container input,
        .register-container select,
        .register-container button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            display: block;
            box-sizing: border-box;
        }

        .register-container input:focus,
        .register-container select:focus,
        .register-container button:focus {
            outline: none;
            border-color: #6a11cb;
        }

        .register-container button {
            background: #6a11cb;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .register-container button:hover {
            background: #2575fc;
        }

        .register-container p {
            margin-top: 1rem;
            margin: 0 auto;
            color: #666;
        }

        .register-container p a {
            color: #6a11cb;
            text-decoration: none;
        }

        .register-container p a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
        }

        .success-message {
            background: #dff0d8;
            color: #3c763d;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 1rem auto;
            max-width: 400px;
            animation: fadeIn 1s forwards;
        }

        .success-message h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .success-message p {
            margin: 0.5rem 0 0;
            font-size: 1rem;
        }

        .fade-out {
            animation: fadeOut 1s forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
                transform: translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateY(-30px);
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Registrarse</h2>
        <?php if (isset($error)) {
            echo "<p class='error-message'>$error</p>";
        } ?>
        <form action="registrarse.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <select name="perfil_id" required>
                <option value="4">Demandante</option>
                <option value="3">Ofertante</option>
            </select>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
        <br>
        <a href="../index.php">Volver a la página web</a>
    </div>
</body>

</html>