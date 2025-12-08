<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

$conexion = conectarPDO($host, $user, $password, $bbdd);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $perfil_id = $_POST['perfil_id'];
    $activo = $_POST['activo'];
    $updated_at = date('Y-m-d H:i:s');

    // Verificar permisos
    if ($_SESSION['usuario']['perfil_id'] != 1) {
        echo "No tienes permisos para modificar este usuario.";
        exit;
    }

    // Actualizar el usuario
    $query = "UPDATE usuarios SET nombre = :nombre, email = :email, perfil_id = :perfil_id, activo = :activo, updated_at = :updated_at WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
    $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);
    $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        header('Location: lista_usuarios.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    if (isset($_GET['usuario_id'])) {
        $usuario_id = $_GET['usuario_id'];
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Error: No se ha proporcionado un ID de usuario.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <main class="max-w-4xl mx-auto p-8 bg-gray-800 rounded-lg shadow-lg w-full">
        <h2 class="text-3xl font-semibold mb-6 text-center">Modificar Usuario</h2>
        <?php if ($usuario): ?>
            <form method="POST" action="modificar_usuario.php" class="flex flex-col space-y-4">
                <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" class="p-3 rounded border border-gray-700 bg-gray-700 text-white w-full" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($usuario['email']); ?>" class="p-3 rounded border border-gray-700 bg-gray-700 text-white w-full" required>
                <select name="perfil_id" class="p-3 rounded border border-gray-700 bg-gray-700 text-white w-full" required>
                    <option value="3" <?php if ($usuario['perfil_id'] == 3) echo 'selected'; ?>>Ofertante</option>
                    <option value="4" <?php if ($usuario['perfil_id'] == 4) echo 'selected'; ?>>Demandante</option>
                </select>
                <select name="activo" class="p-3 rounded border border-gray-700 bg-gray-700 text-white w-full" required>
                    <option value="1" <?php if ($usuario['activo'] == 1) echo 'selected'; ?>>Activo</option>
                    <option value="0" <?php if ($usuario['activo'] == 0) echo 'selected'; ?>>Inactivo</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Modificar Usuario</button>
            </form>
        <?php else: ?>
            <p class="text-center text-red-500">Error: Usuario no encontrado.</p>
        <?php endif; ?>
        <div class="mt-6 text-center">
            <a href="lista_usuarios.php" class="text-blue-400 hover:underline">Volver a la lista de usuarios</a>
        </div>
    </main>
</body>

</html>