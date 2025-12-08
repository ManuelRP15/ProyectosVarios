<?php
require_once("../variables.php");
require_once("../funciones.php");

$mensaje = ""; // Variable para almacenar mensajes de éxito o error
$redirect = "login.php"; // Página a la que se redirige automáticamente después de unos segundos

if (isset($_GET['token'])) { // Verifica si se recibe un token vía URL
    $token = $_GET['token'];

    try {
        $conexion = conectarPDO($host, $user, $password, $bbdd); // Conecta a la base de datos

        // Busca el usuario que tenga el token recibido
        $query = "SELECT * FROM usuarios WHERE token = :token";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Si el token es válido: activa la cuenta y borra el token
            $query = "UPDATE usuarios SET activo = 1, token = '' WHERE token = :token";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            $mensaje = "Correo electrónico verificado exitosamente. Ahora puedes iniciar sesión.";
        } else {
            // Si el token no existe en la base de datos
            $mensaje = "Token inválido.";
        }
    } catch (PDOException $e) {
        // Captura errores de conexión o ejecución
        $mensaje = "Error de conexión: " . $e->getMessage();
    }
} else {
    // Si no se recibe token en la URL
    $mensaje = "Token no proporcionado.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(45deg, #00796b, #004d40);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            text-align: center;
            flex-direction: column;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .message {
            margin: 2rem 0;
            padding: 1rem;
            background-color: #ffffff;
            color: #333;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
        }

        .success {
            background-color: #a5d6a7;
            color: #388e3c;
        }

        .error {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .link {
            color: rgb(236, 255, 225);
            text-decoration: none;
            font-weight: bold;
        }

        .link:hover {
            text-decoration: underline;
        }

        .redirect {
            margin-top: 2rem;
            font-size: 1.1rem;
            color: #fff;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
    </style>
    <meta http-equiv="refresh" content="5;url=<?php echo $redirect; ?>">
</head>

<body>
    <div class="container fade-in">
        <h1>Verificación de Correo</h1>

        <?php if (strpos($mensaje, 'exitosamente') !== false): ?>
            <div class="message success"><?php echo $mensaje; ?></div>
        <?php else: ?>
            <div class="message error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="redirect">
            Serás redirigido a la página de <a href="<?php echo $redirect; ?>" class="link">inicio de sesión</a> en unos segundos...
        </div>
    </div>
</body>

</html>