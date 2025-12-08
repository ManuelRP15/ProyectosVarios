<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

$conexion = conectarPDO($host, $user, $password, $bbdd);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['oferta_id'])) {
    $oferta_id = $_POST['oferta_id'];

    // Obtener la oferta actual
    $query = "SELECT * FROM ofertas WHERE id = :oferta_id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
    $stmt->execute();
    $oferta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oferta) {
        echo "Oferta no encontrada.";
        exit;
    }

    // Verificar permisos
    $perfil_id = $_SESSION['perfil_temporal'] ?? $_SESSION['usuario']['perfil_id'] ?? null;
    if ($perfil_id != 1 && $perfil_id != 3 && $perfil_id != 2 && $oferta['usuario_id'] != $_SESSION['usuario']['id']) {
        echo "No tienes permisos para modificar esta oferta.";
        exit;
    }

    if (isset($_POST['nombre'])) {
        // Actualizar la oferta
        $query = "UPDATE ofertas SET nombre = :nombre, descripcion = :descripcion, fecha_actividad = :fecha_actividad, aforo = :aforo, categoria_id = :categoria_id WHERE id = :oferta_id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $_POST['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_actividad', $_POST['fecha_actividad'], PDO::PARAM_STR);
        $stmt->bindParam(':aforo', $_POST['aforo'], PDO::PARAM_INT);
        $stmt->bindParam(':categoria_id', $_POST['categoria_id'], PDO::PARAM_INT);
        $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirigir a la página anterior
        header('Location: index.php');
        exit;
    }
} else {
    echo "Método no permitido.";
    exit;
}

// Obtener categorías
$query = "SELECT * FROM categorias";
$stmt = $conexion->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Oferta</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .fade-out {
            animation: fadeOut 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <main class="w-full max-w-6xl mx-auto p-6 fade-in">
        <h2 class="text-4xl font-bold mb-8 text-center">Modificar Oferta</h2>
        <form method="POST" action="modificar_oferta.php" class="bg-gray-800 p-10 rounded-lg shadow-md w-full max-w-4xl mx-auto">
            <input type="hidden" name="oferta_id" value="<?php echo htmlspecialchars($oferta['id']); ?>">
            <div class="mb-6">
                <label for="nombre" class="block text-white">Nombre de la Oferta</label>
                <input type="text" name="nombre" id="nombre" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" value="<?php echo htmlspecialchars($oferta['nombre']); ?>" required>
            </div>
            <div class="mb-6">
                <label for="descripcion" class="block text-white">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" required><?php echo htmlspecialchars($oferta['descripcion']); ?></textarea>
            </div>
            <div class="mb-6">
                <label for="fecha_actividad" class="block text-white">Fecha de la Actividad</label>
                <input type="datetime-local" name="fecha_actividad" id="fecha_actividad" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" value="<?php echo htmlspecialchars($oferta['fecha_actividad']); ?>" required>
            </div>
            <div class="mb-6">
                <label for="aforo" class="block text-white">Aforo</label>
                <input type="number" name="aforo" id="aforo" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" value="<?php echo htmlspecialchars($oferta['aforo']); ?>" required>
            </div>
            <div class="mb-6">
                <label for="categoria_id" class="block text-white">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php if ($oferta['categoria_id'] == $categoria['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($categoria['categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">Guardar Cambios</button>
        </form>
        <div class="text-center mt-6">
            <a href="index.php" class="text-blue-500 hover:text-white">Volver al inicio</a>
        </div>
    </main>
</body>

</html>