<?php
session_start();
require_once("../funciones.php");
require_once("../variables.php");

// Si el usuario ya está logueado, redirigirlo al inicio para evitar que vuelva a iniciar sesión
if (isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

$error = ''; // Variable para almacenar mensajes de error en el login

// Verificar si se envió el formulario mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = conectarPDO($host, $user, $password, $bbdd); // Conectar a la base de datos
    $email = $_POST['email']; // Correo ingresado
    $password = $_POST['password']; // Contraseña ingresada

    // Buscar en la tabla de usuarios un usuario con el correo proporcionado
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener el usuario como array asociativo

    // Verificar que el usuario exista y que la contraseña coincida
    if ($user && password_verify($password, $user['password'])) {
        // Comprobar si la cuenta está activada
        if ($user['activo'] == 1) {
            $_SESSION['usuario'] = $user; // Guardar los datos del usuario en sesión
            header('Location: ../index.php'); // Redirigir al inicio
            exit;
        } else {
            $error = 'Tu cuenta no está activada. Por favor, verifica tu correo electrónico.'; // Mensaje si la cuenta está inactiva
        }
    } else {
        $error = 'Correo o contraseña incorrectos.'; // Mensaje si login falla
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">

    <!-- Estilos del formulario de login con efectos visuales y animaciones -->
    <style>
        /* Reset y estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            animation: fadeIn 1s ease-in-out;
        }

        /* Contenedor del login con fondo semi-transparente y efecto blur */
        .login-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 400px;
            text-align: center;
            animation: slideIn 1s ease-out;
        }

        .login-container h2 {
            margin-bottom: 25px;
            color: white;
            font-size: 28px;
            font-weight: bold;
        }

        /* Estilos para los campos de entrada */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: white;
            font-weight: bold;
        }

        .input-group input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input:focus {
            border: 2px solid #4facfe;
            box-shadow: 0 0 8px rgba(79, 172, 254, 0.8);
        }

        /* Mensaje de error */
        .error-message {
            color: #ff6363;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Botón de login */
        .login-btn {
            width: 100%;
            padding: 14px;
            background: #4facfe;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: #00c6ff;
        }

        /* Enlaces debajo del formulario */
        .links {
            margin-top: 15px;
        }

        .links a {
            display: block;
            font-size: 15px;
            color: #ffffff;
            text-decoration: none;
            margin-top: 10px;
            transition: opacity 0.3s;
        }

        .links a:hover {
            opacity: 0.7;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form method="post">
            <div class="input-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" placeholder="Introduce tu email"
                    value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" placeholder="Introduce tu contraseña" required>
            </div>

            <button type="submit" class="login-btn">Iniciar sesión</button>
        </form>

        <!-- Enlaces de navegación -->
        <div class="links">
            <a href="recordar.php">¿Olvidaste tu contraseña?</a>
            <a href="registrarse.php">¿No tienes cuenta? Regístrate</a>
            <a href="../index.php">Volver a la página web</a>
        </div>
    </div>

</body>

</html>