<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Comprobamos que la petición llegue por POST y que el usuario esté logueado.
// Además, solo permitimos continuar si el perfil del usuario es 4 o 1.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && ($_SESSION['usuario']['perfil_id'] == 4 || $_SESSION['usuario']['perfil_id'] == 1)) {

    // Guardamos el ID del usuario y el de la oferta que llega desde el formulario.
    $usuario_id = $_SESSION['usuario']['id'];
    $oferta_id = $_POST['oferta_id'];

    // Conexión a la base de datos usando PDO.
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    // Preparamos la inserción de la solicitud en la tabla.
    $query = "INSERT INTO solicitudes (oferta_id, usuario_id, fecha_solicitud, created_at, updated_at) 
              VALUES (:oferta_id, :usuario_id, NOW(), NOW(), NOW())";

    $stmt = $conexion->prepare($query);

    // Asociamos los parámetros para evitar inyecciones SQL.
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

    // Si la inserción funciona, volvemos a la página principal.
    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        // Si falla, mostramos un mensaje sencillo de error.
        echo "Error al apuntarse a la oferta.";
    }
} else {
    // Si se intenta acceder directamente o sin permisos, redirigimos al inicio.
    header('Location: index.php');
    exit();
}
