<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Obtener categorías
$query = "SELECT * FROM categorias";
$stmt = $conexion->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar ofertas
$filtro_categoria = $_GET['categoria'] ?? '';
$filtro_orden = $_GET['orden'] ?? 'fecha_actividad ASC';
$solo_apuntadas = isset($_GET['solo_apuntadas']) ? true : false;
$mis_ofertas = isset($_GET['mis_ofertas']) ? true : false;
$sin_visar = isset($_GET['sin_visar']) ? true : false;

// Verificar si el administrador ha seleccionado un perfil temporal
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1 && isset($_POST['perfil_temporal'])) {
    $_SESSION['perfil_temporal'] = $_POST['perfil_temporal'];
} elseif (isset($_POST['reset_perfil_temporal'])) {
    unset($_SESSION['perfil_temporal']);
}

$perfil_id = $_SESSION['perfil_temporal'] ?? $_SESSION['usuario']['perfil_id'] ?? null;

$query = "SELECT ofertas.*, categorias.categoria, 
          (SELECT COUNT(*) FROM solicitudes WHERE solicitudes.oferta_id = ofertas.id) AS num_apuntados 
          FROM ofertas 
          JOIN categorias ON ofertas.categoria_id = categorias.id 
          WHERE 1=1";

if ($filtro_categoria) {
    $query .= " AND ofertas.categoria_id = :categoria_id";
}

if ($solo_apuntadas && ($perfil_id == 4 || $perfil_id == 1)) {
    $query .= " AND ofertas.id IN (SELECT solicitudes.oferta_id FROM solicitudes WHERE solicitudes.usuario_id = :usuario_id)";
}

if ($mis_ofertas && $perfil_id == 3) {
    $query .= " AND ofertas.usuario_id = :usuario_id";
}

if ($sin_visar && $perfil_id == 2) {
    $query .= " AND ofertas.visada = 0";
}

// Filtrar ofertas no visadas para demandantes y usuarios no loggeados
if (!isset($_SESSION['usuario']) || ($perfil_id != 2 && $perfil_id != 1 && $perfil_id != 3 && $perfil_id != 4)) {
    $query .= " AND ofertas.visada = 1";
} elseif ($perfil_id == 3) {
    $query .= " AND (ofertas.visada = 1 OR ofertas.usuario_id = :usuario_id)";
}

$query .= " ORDER BY $filtro_orden";
$stmt = $conexion->prepare($query);

if ($filtro_categoria) {
    $stmt->bindParam(':categoria_id', $filtro_categoria, PDO::PARAM_INT);
}

if (($solo_apuntadas && ($perfil_id == 4 || $perfil_id == 1)) || ($mis_ofertas && $perfil_id == 3) || ($perfil_id == 3)) {
    $stmt->bindParam(':usuario_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
}

$stmt->execute();
$rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventureros Natos</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header id="main-header" class="fixed w-full bg-gray-900 shadow-md py-4 px-6 hidden-header">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Aventureros Natos</h1>
            <nav>
                <ul class="flex space-x-4">
                    <?php if (!isset($_SESSION['usuario'])): ?>
                        <li>
                            <a href="acceso/login.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Iniciar sesión</a>
                        </li>
                        <li>
                            <a href="acceso/registrarse.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Registrarse</a>
                        </li>
                        <li>
                            <a href="acceso/login_admin.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Login Admin/Gestor</a>
                        </li>
                    <?php else: ?>
                        <li><a href="perfil.php" class="text-white">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? ''); ?></a></li>
                        <li><a href="acceso/logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Cerrar sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="pt-20 max-w-7xl mx-auto p-6">
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1): // Administrador 
        ?>
            <section class="mb-6 admin-section">
                <h2 class="text-2xl font-semibold mb-4 text-center text-white">Panel de Administración</h2>
                <div class="flex justify-center space-x-4">
                    <a href="crear_oferta.php" class="bg-green-500 text-white px-6 py-3 rounded-xl shadow-md hover:bg-green-600 transform transition duration-300 ease-in-out hover:scale-105">Crear Nueva Oferta</a>
                    <a href="lista_usuarios.php" class="bg-purple-500 text-white px-6 py-3 rounded-xl shadow-md hover:bg-purple-600 transform transition duration-300 ease-in-out hover:scale-105">Ver Lista de Usuarios</a>
                </div>
            </section>
        <?php endif; ?>

        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1): // Administrador 
        ?>
            <section class="mb-6 admin-section">
                <div class="flex flex-col items-center space-y-4 w-full">
                    <form method="POST" class="flex flex-col items-center space-y-4 w-3/4">
                        <select name="perfil_temporal"
                            class="p-3 w-full rounded-lg border border-gray-300 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out text-gray-700">
                            <option value="">Modo Dios</option>
                            <option value="4" <?php if ($perfil_id == 4) echo 'selected'; ?>>Demandante</option>
                            <option value="3" <?php if ($perfil_id == 3) echo 'selected'; ?>>Ofertante</option>
                            <option value="2" <?php if ($perfil_id == 2) echo 'selected'; ?>>Gestor</option>
                        </select>
                        <button type="submit"
                            class="bg-blue-500 text-white px-6 py-3 w-full rounded-xl shadow-md hover:bg-blue-600 transform transition duration-300 ease-in-out hover:scale-105">
                            Cambiar Vista
                        </button>
                    </form>

                    <form method="POST" class="w-3/4">
                        <button type="submit" name="reset_perfil_temporal"
                            class="bg-red-500 text-white px-6 py-3 w-full rounded-xl shadow-md hover:bg-red-600 transform transition duration-300 ease-in-out hover:scale-105">
                            Restablecer Vista
                        </button>
                    </form>
                </div>
            </section>


        <?php endif; ?>

        <?php if ($perfil_id == 3): // Ofertante o Administrador 
        ?>
            <section>
                <div class="flex justify-center space-x-4">
                    <a href="crear_oferta.php" class="bg-green-500 text-white px-6 py-3 rounded-xl shadow-md hover:bg-green-600 transform transition duration-300 ease-in-out hover:scale-105">Crear Nueva Oferta</a>
                </div>
            </section>
            <br>
        <?php endif; ?>

        <section class="mb-6 filter-section">
            <form method="GET" class="flex flex-wrap justify-center w-full max-w-4xl mx-auto">
                <select name="categoria" class="p-2 rounded border bg-gray-700 text-white mb-2 md:mb-0">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php if ($filtro_categoria == $categoria['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($categoria['categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="orden" class="p-2 rounded border bg-gray-700 text-white mb-2 md:mb-0">
                    <option value="fecha_actividad ASC" <?php if ($filtro_orden == 'fecha_actividad ASC') echo 'selected'; ?>>Fecha ascendente</option>
                    <option value="fecha_actividad DESC" <?php if ($filtro_orden == 'fecha_actividad DESC') echo 'selected'; ?>>Fecha descendente</option>
                    <option value="aforo ASC" <?php if ($filtro_orden == 'aforo ASC') echo 'selected'; ?>>Aforo ascendente</option>
                    <option value="aforo DESC" <?php if ($filtro_orden == 'aforo DESC') echo 'selected'; ?>>Aforo descendente</option>
                </select>

                <?php if ($perfil_id == 4 || $perfil_id == 1): ?>
                    <label class="flex items-center text-white mb-2 md:mb-0">
                        <input type="checkbox" name="solo_apuntadas" value="1" class="mr-2" <?php if ($solo_apuntadas) echo 'checked'; ?>>
                        Ofertas a las que estoy apuntado
                    </label>
                <?php endif; ?>

                <?php if ($perfil_id == 3): ?>
                    <label class="flex items-center text-white mb-2 md:mb-0">
                        <input type="checkbox" name="mis_ofertas" value="1" class="mr-2" <?php if ($mis_ofertas) echo 'checked'; ?>>
                        Mis ofertas
                    </label>
                <?php endif; ?>

                <?php if ($perfil_id == 2): // Gestor 
                ?>
                    <label class="flex items-center text-white mb-2 md:mb-0">
                        <input type="checkbox" name="sin_visar" value="1" class="mr-2" <?php if ($sin_visar) echo 'checked'; ?>>
                        Ofertas sin visar
                    </label>
                <?php endif; ?>

                <button type="submit" class="filter-btn text-white px-4 py-2 rounded hover:bg-blue-600">Filtrar</button>
            </form>
        </section>


        <section>
            <h2 class="text-3xl font-semibold mb-6 text-center text-white">Ofertas de Rutas</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <?php
                foreach ($rutas as $ruta) {
                    echo "<div class='offer-card'>";
                    echo "<h3>" . htmlspecialchars($ruta['nombre']) . "</h3>";
                    echo "<p><strong>Fecha:</strong> " . htmlspecialchars($ruta['fecha_actividad']) . "</p>";
                    echo "<p><strong>Aforo:</strong> " . htmlspecialchars($ruta['aforo']) . " personas</p>";
                    echo "<p><strong>Categoría:</strong> " . htmlspecialchars($ruta['categoria']) . "</p>";
                    echo "<p><strong>Visada:</strong> " . ($ruta['visada'] ? 'Sí' : 'No') . "</p>";
                    echo "<p><strong>Apuntados:</strong> " . htmlspecialchars($ruta['num_apuntados']) . "</p>";
                    echo "<p>" . nl2br(htmlspecialchars($ruta['descripcion'])) . "</p>";

                    if (isset($_SESSION['usuario'])) {
                        if ($perfil_id == 4) { // Demandante
                            $fecha_actividad = new DateTime($ruta['fecha_actividad']);
                            $fecha_actual = new DateTime();
                            $intervalo = $fecha_actual->diff($fecha_actividad)->days;

                            if ($intervalo <= 15) {
                                echo "<button class='bg-gray-500 text-white px-4 py-2 rounded mt-4' disabled>No disponible</button>";
                            } elseif ($ruta['num_apuntados'] >= $ruta['aforo']) {
                                echo "<button class='bg-gray-500 text-white px-4 py-2 rounded mt-4' disabled>Oferta completa</button>";
                            } else {
                                $query_apuntado = "SELECT * FROM solicitudes WHERE usuario_id = :usuario_id AND oferta_id = :oferta_id";
                                $stmt_apuntado = $conexion->prepare($query_apuntado);
                                $stmt_apuntado->bindParam(':usuario_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
                                $stmt_apuntado->bindParam(':oferta_id', $ruta['id'], PDO::PARAM_INT);
                                $stmt_apuntado->execute();
                                $apuntado = $stmt_apuntado->fetch(PDO::FETCH_ASSOC);

                                if ($apuntado) {
                                    echo "<form method='POST' action='desapuntarse.php'>";
                                    echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                    echo "<button type='submit' class='bg-red-500 text-white px-4 py-2 rounded mt-4'>Desapuntarse</button>";
                                    echo "</form>";
                                } else {
                                    echo "<form method='POST' action='apuntarse.php'>";
                                    echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                    echo "<button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded mt-4'>Apuntarse</button>";
                                    echo "</form>";
                                }
                            }
                        } elseif ($perfil_id == 3 && $ruta['usuario_id'] == $_SESSION['usuario']['id']) { // Ofertante
                            echo "<form method='POST' action='borrar_oferta.php'>";
                            echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                            echo "<button type='submit' class='bg-red-500 text-white px-4 py-2 rounded mt-4'>Borrar</button>";
                            echo "</form>";

                            if (!$ruta['visada']) {
                                echo "<form method='POST' action='modificar_oferta.php'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                echo "<button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded mt-4'>Modificar</button>";
                                echo "</form>";
                            } else {
                                echo "<button class='bg-gray-500 text-white px-4 py-2 rounded mt-4' disabled>Oferta ya visada</button>";
                            }
                        } elseif ($perfil_id == 2) { // Gestor
                            if (!$ruta['visada']) {
                                echo "<form method='POST' action='visar_oferta.php'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                echo "<input type='hidden' name='accion' value='visar'>";
                                echo "<button type='submit' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Visar</button>";
                                echo "</form>";

                                echo "<form method='POST' action='borrar_oferta.php'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                echo "<button type='submit' class='bg-red-500 text-white px-4 py-2 rounded mt-4'>Rechazar</button>";
                                echo "</form>";
                            } else {
                                echo "<button class='bg-gray-500 text-white px-4 py-2 rounded mt-4' disabled>Oferta ya visada</button>";
                            }
                        } elseif ($perfil_id == 1) { // Administrador
                            echo "<form method='POST' action='borrar_oferta.php'>";
                            echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                            echo "<button type='submit' class='bg-red-500 text-white px-4 py-2 rounded mt-4'>Borrar</button>";
                            echo "</form>";

                            echo "<form method='POST' action='modificar_oferta.php'>";
                            echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                            echo "<button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded mt-4'>Modificar</button>";
                            echo "</form>";

                            if (!$ruta['visada']) {
                                echo "<form method='POST' action='visar_oferta.php'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                echo "<input type='hidden' name='accion' value='visar'>";
                                echo "<button type='submit' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Visar</button>";
                                echo "</form>";
                            } else {
                                echo "<form method='POST' action='visar_oferta.php'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $ruta['id'] . "'>";
                                echo "<input type='hidden' name='accion' value='desvisar'>";
                                echo "<button type='submit' class='bg-yellow-500 text-white px-4 py-2 rounded mt-4'>Desvisar</button>";
                                echo "</form>";
                            }
                        }
                    } else {
                        $fecha_actividad = new DateTime($ruta['fecha_actividad']);
                        $fecha_actual = new DateTime();
                        $intervalo = $fecha_actual->diff($fecha_actividad)->days;

                        if ($intervalo <= 15) {
                            echo "<button class='bg-gray-500 text-white px-4 py-2 rounded mt-4' disabled>No disponible</button>";
                        } else {
                            echo "<a href='acceso/login.php' class='bg-blue-500 text-white px-4 py-2 rounded mt-4'>Iniciar sesión para apuntarse</a>";
                        }
                    }

                    echo "</div>";
                }
                ?>
            </div>
        </section>
        <br><br>

    </main>
</body>

</html>