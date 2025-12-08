<?php
require_once("../funciones.php");
require_once("../variables.php");

$mensaje = ""; // Variable para almacenar mensajes de error o éxito

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica que se envió el formulario
    $token = $_POST['token']; // Token de restablecimiento recibido
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashea la nueva contraseña

    try {
        $conexion = conectarPDO($host, $user, $password, $bbdd); // Conecta a la base de datos

        // Busca el usuario que tenga el token recibido
        $query = "SELECT * FROM usuarios WHERE token = :token";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            // Verifica si la nueva contraseña es igual a la actual
            if (password_verify($_POST['password'], $usuario['password'])) {
                $mensaje = "<p class='mensaje-error'>La nueva contraseña no puede ser igual a la actual.</p>";
            } else {
                // Si la contraseña es distinta: actualiza la contraseña y limpia el token
                $query = "UPDATE usuarios SET password = :password, token = '' WHERE token = :token";
                $stmt = $conexion->prepare($query);
                $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();

                $mensaje = "<p class='mensaje-exito'>Contraseña restablecida exitosamente. Ahora puedes <a href='login.php'>iniciar sesión</a>.</p>";
            }
        } else {
            // Si no se encuentra el token en la base de datos
            $mensaje = "<p class='mensaje-error'>Token inválido.</p>";
        }
    } catch (PDOException $e) {
        // Captura errores de conexión o ejecución
        $mensaje = "<p class='mensaje-error'>Error de conexión: " . $e->getMessage() . "</p>";
    }
} elseif (isset($_GET['token'])) {
    // Si se recibe el token por GET (al entrar al enlace del correo)
    $token = $_GET['token'];
} else {
    // Si no se proporciona token
    echo "<p class='mensaje-error'>Token no proporcionado.</p>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body {
            background-color: #1f2937;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: #111827;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .input-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        input,
        button {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: 12px 0;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 1.1rem;
            box-sizing: border-box;
        }

        input {
            background-color: #374151;
            color: #ffffff;
            text-align: center;
        }

        button {
            background-color: #3b82f6;
            color: #ffffff;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2563eb;
        }

        .form-footer {
            margin-top: 1rem;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .form-footer a:hover {
            color: #2563eb;
        }

        .mensaje-exito,
        .mensaje-error {
            font-size: 1.2rem;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .mensaje-exito {
            background-color: #10b981;
            color: #ffffff;
        }

        .mensaje-error {
            background-color: #ef4444;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="title">Restablecer Contraseña</h2>
        <form action="restablecer.php" method="POST"> <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="input-container"> <input type="password" name="password" placeholder="Nueva Contraseña" required> </div>
            <div class="input-container"> <button type="submit">Restablecer</button> </div>
        </form>
        <div class="form-footer"> <a href="login.php">Volver al inicio de sesión</a> </div> <?php echo $mensaje; ?>
    </div>
</body>

</html>