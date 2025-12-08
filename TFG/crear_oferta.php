<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Verificar si el usuario está logueado y tiene el perfil adecuado (administrador o ofertante)
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['perfil_id'] != 1 && $_SESSION['usuario']['perfil_id'] != 3)) {
    header('Location: index.php');
    exit;
}

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Obtener categorías
$query = "SELECT id, categoria FROM categorias";
$stmt = $conexion->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = ''; // Variable para almacenar errores

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_actividad = $_POST['fecha_actividad'] . ' ' . $_POST['hora_actividad'];
    $aforo = $_POST['aforo'];
    $categoria_id = $_POST['categoria_id'];
    $usuario_id = $_SESSION['usuario']['id'];
    $visada = ($_SESSION['usuario']['perfil_id'] == 1) ? 1 : 0;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Validar fecha
    $fecha_hoy = date('Y-m-d H:i:s');
    if (strtotime($fecha_actividad) < strtotime($fecha_hoy)) {
        $error = "No se pueden crear ofertas con fecha anterior al día de hoy.";
    } else {
        $query = "INSERT INTO ofertas (nombre, descripcion, fecha_actividad, aforo, categoria_id, usuario_id, visada, created_at, updated_at) 
                  VALUES (:nombre, :descripcion, :fecha_actividad, :aforo, :categoria_id, :usuario_id, :visada, :created_at, :updated_at)";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_actividad', $fecha_actividad, PDO::PARAM_STR);
        $stmt->bindParam(':aforo', $aforo, PDO::PARAM_INT);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':visada', $visada, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

        try {
            $stmt->execute();
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Oferta</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/styles.css">
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
        <h2 class="text-4xl font-bold mb-8 text-center">Crear Nueva Oferta</h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-600 text-white p-4 mb-6 rounded text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="crear_oferta.php" class="bg-gray-800 p-10 rounded-lg shadow-md w-full max-w-4xl mx-auto">
            <div class="mb-6">
                <label for="nombre" class="block text-white">Nombre de la oferta</label>
                <input type="text" name="nombre" id="nombre" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" placeholder="Nombre de la oferta" required>
            </div>
            <div class="mb-6">
                <label for="descripcion" class="block text-white">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" placeholder="Descripción" rows="4" required></textarea>
            </div>
            <div class="mb-6">
                <label for="fecha_actividad" class="block text-white">Fecha de la actividad</label>
                <input type="date" name="fecha_actividad" id="fecha_actividad" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" required>
            </div>
            <div class="mb-6">
                <label for="hora_actividad" class="block text-white">Hora de la actividad</label>
                <input type="time" name="hora_actividad" id="hora_actividad" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" required>
            </div>
            <div class="mb-6">
                <label for="aforo" class="block text-white">Aforo</label>
                <input type="number" name="aforo" id="aforo" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" placeholder="Aforo" required>
            </div>
            <div class="mb-6">
                <label for="categoria_id" class="block text-white">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="w-full p-4 rounded border border-gray-700 bg-gray-700 text-white" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['categoria']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">Crear Oferta</button>
        </form>
        <div class="text-center mt-6">
            <a href="index.php" class="text-blue-500 hover:text-white">Volver al inicio</a>
        </div>
    </main>
</body>

</html>