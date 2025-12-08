<?php
session_start();

require_once("funciones.php");
require_once("variables.php");

// Compruebo que la petición es POST, que el usuario está logueado
// y que su perfil es válido (admin o candidato)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && ($_SESSION['usuario']['perfil_id'] == 4 || $_SESSION['usuario']['perfil_id'] == 1)) {

    // Obtengo el ID del usuario desde la sesión
    $usuario_id = $_SESSION['usuario']['id'];
    // Obtengo la oferta desde el formulario
    $oferta_id = $_POST['oferta_id'];

    // Conexión a la base de datos
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    // Preparo la consulta para eliminar la solicitud
    $query = "DELETE FROM solicitudes WHERE oferta_id = :oferta_id AND usuario_id = :usuario_id";
    $stmt = $conexion->prepare($query);

    // Vinculo los parámetros para evitar inyecciones SQL
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

    // Ejecuto la consulta y redirijo según el resultado
    if ($stmt->execute()) {
        header('Location: index.php'); // Vuelvo a la página principal
        exit();
    } else {
        echo "Error al desapuntarse de la oferta."; // Mensaje de error si falla
    }
} else {
    // Si no pasa las validaciones, vuelvo al inicio
    header('Location: index.php');
    exit();
}
