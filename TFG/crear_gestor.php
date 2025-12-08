<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Solo un administrador (perfil 1) puede acceder a esta página.
// Si no cumple los requisitos, lo mandamos al inicio.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil_id'] != 1) {
    header('Location: index.php');
    exit;
}

// Conexión a la base de datos.
$conexion = conectarPDO($host, $user, $password, $bbdd);

// Si se ha enviado el formulario, procesamos los datos.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    // Encriptamos la contraseña antes de guardarla por seguridad.
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insertamos un nuevo gestor en la base de datos.
    // Le asignamos perfil 2 porque es el rol de gestor.
    $query = "INSERT INTO gestores (nombre, email, password, perfil_id, created_at, updated_at) 
              VALUES (:nombre, :email, :password, 2, NOW(), NOW())";

    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    try {
        // Intentamos ejecutar el insert; si todo va bien volvemos al inicio.
        $stmt->execute();
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        // Si ocurre un error lo mostramos en pantalla.
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Gestor</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <main class="max-w-4xl mx-auto p-6 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold mb-6 text-center">Crear Nuevo Gestor</h2>

        <!-- Formulario para crear un nuevo gestor -->
        <form method="POST" class="flex flex-col space-y-4">
            <input type="text" name="nombre" placeholder="Nombre del gestor"
                class="p-3 rounded border border-gray-700 bg-gray-700 text-white" required>

            <input type="email" name="email" placeholder="Email del gestor"
                class="p-3 rounded border border-gray-700 bg-gray-700 text-white" required>

            <input type="password" name="password" placeholder="Contraseña"
                class="p-3 rounded border border-gray-700 bg-gray-700 text-white" required>

            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Crear Gestor
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-400 hover:underline">
                Volver a la página principal
            </a>
        </div>
    </main>
</body>

</html>