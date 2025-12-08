<?php
function conectarPDO($host, $user, $password, $bbdd)
{
    try {
        // Construyo el DSN con los datos de conexión y el charset adecuado
        $dsn = "mysql:host=$host;dbname=$bbdd;charset=utf8mb4";
        // Creo la conexión con PDO
        $conexion = new PDO($dsn, $user, $password);

        // Activo las excepciones para manejar errores de forma más controlada
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conexion; // Devuelvo la conexión lista para usar
    } catch (PDOException $e) {
        // Si falla la conexión, muestro el error y detengo la ejecución
        echo "Error de conexión: " . $e->getMessage();
        exit;
    }
}

function apuntarseOferta($usuario_id, $oferta_id)
{
    global $conexion; // Uso la conexión global creada previamente

    // Inserto una nueva solicitud con la fecha actual
    $query = "INSERT INTO solicitudes (usuario_id, oferta_id, fecha_solicitud) VALUES (:usuario_id, :oferta_id, NOW())";
    $stmt = $conexion->prepare($query);

    // Asigno los valores para evitar inyección SQL
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);

    return $stmt->execute(); // Ejecuto y devuelvo si ha ido bien
}

function obtenerCategorias()
{
    global $conexion; // Reutilizo la conexión existente

    // Obtengo todas las categorías de la tabla
    $query = "SELECT * FROM categorias";
    $stmt = $conexion->prepare($query);
    $stmt->execute();

    // Devuelvo todas las filas como array asociativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
