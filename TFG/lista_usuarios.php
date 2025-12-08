<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Solo permitir acceso a administradores
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1) {
    $conexion = conectarPDO($host, $user, $password, $bbdd); // Conexión a la base de datos

    // Obtener todos los usuarios
    $queryUsuarios = "SELECT * FROM usuarios";
    $stmtUsuarios = $conexion->prepare($queryUsuarios);
    $stmtUsuarios->execute();
    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los gestores
    $queryGestores = "SELECT * FROM gestores";
    $stmtGestores = $conexion->prepare($queryGestores);
    $stmtGestores->execute();
    $gestores = $stmtGestores->fetchAll(PDO::FETCH_ASSOC);

    // Obtener nombres de perfiles y mapearlos por ID
    $queryPerfiles = "SELECT * FROM perfiles";
    $stmtPerfiles = $conexion->prepare($queryPerfiles);
    $stmtPerfiles->execute();
    $perfiles = $stmtPerfiles->fetchAll(PDO::FETCH_ASSOC);
    $perfilNombres = [];
    foreach ($perfiles as $perfil) {
        $perfilNombres[$perfil['id']] = $perfil['perfil'];
    }
} else {
    // Redirigir si no es administrador
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios y Gestores</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white">
    <main class="max-w-7xl mx-auto p-6">
        <h2 class="text-4xl font-bold mb-8 text-center">Lista de Usuarios y Gestores</h2>

        <!-- Tabla de Usuarios -->
        <section class="mb-12">
            <h3 class="text-3xl font-semibold mb-6 text-center">Usuarios</h3>
            <table class="min-w-full bg-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-gray-700">
                        <th>ID</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Perfil</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr class="border-b border-gray-700">
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($perfilNombres[$usuario['perfil_id']])); ?></td>
                            <td><?php echo htmlspecialchars($usuario['activo']); ?></td>
                            <td class="flex justify-center space-x-2">
                                <?php if ($usuario['perfil_id'] != 1): ?>
                                    <!-- Botones para modificar o borrar usuarios que no sean admin -->
                                    <form method="GET" action="modificar_usuario.php">
                                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Modificar</button>
                                    </form>
                                    <form method="POST" action="borrar_usuario.php">
                                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Borrar</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Admin no se puede modificar ni borrar -->
                                    <button class="bg-gray-500 text-white px-4 py-2 rounded" disabled>Prohibido</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Tabla de Gestores -->
        <section class="mb-12">
            <h3 class="text-3xl font-semibold mb-6 text-center">Gestores</h3>
            <table class="min-w-full bg-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-gray-700">
                        <th>ID</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Perfil</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gestores as $gestor): ?>
                        <tr class="border-b border-gray-700">
                            <td><?php echo htmlspecialchars($gestor['id']); ?></td>
                            <td><?php echo htmlspecialchars($gestor['email']); ?></td>
                            <td><?php echo htmlspecialchars($gestor['nombre']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($perfilNombres[$gestor['perfil_id']])); ?></td>
                            <td class="flex justify-center space-x-2">
                                <?php if ($gestor['perfil_id'] != 1): ?>
                                    <!-- Botones para modificar o borrar gestores que no sean admin -->
                                    <form method="GET" action="modificar_gestor.php">
                                        <input type="hidden" name="gestor_id" value="<?php echo $gestor['id']; ?>">
                                        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Modificar</button>
                                    </form>
                                    <form method="POST" action="borrar_usuario.php">
                                        <input type="hidden" name="gestor_id" value="<?php echo $gestor['id']; ?>">
                                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Borrar</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Admin no se puede modificar ni borrar -->
                                    <button class="bg-gray-500 text-white px-4 py-2 rounded" disabled>Prohibido</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Formulario para añadir nuevo usuario -->
        <section>
            <h3 class="text-3xl font-semibold mb-6 text-center">Añadir Nuevo Usuario</h3>
            <form method="POST" action="crear_usuario.php" class="bg-gray-800 p-6 rounded-lg shadow-md max-w-lg mx-auto">
                <div class="mb-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 rounded border border-gray-700 bg-gray-700 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="w-full p-2 rounded border border-gray-700 bg-gray-700 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" class="w-full p-2 rounded border border-gray-700 bg-gray-700 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="perfil_id">Perfil</label>
                    <select name="perfil_id" id="perfil_id" class="w-full p-2 rounded border border-gray-700 bg-gray-700 text-white" required>
                        <option value="2">Gestor</option>
                        <option value="3">Ofertante</option>
                        <option value="4">Demandante</option>
                    </select>
                </div>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Añadir Usuario</button>
            </form>
        </section>

        <!-- Link para volver a la página principal -->
        <div class="mt-6 text-center">
            <a href="index.php" class="text-blue-400 hover:underline">Volver a la página principal</a>
        </div>
    </main>
</body>

</html>