<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Comprobamos que la petición sea POST y que el usuario esté logueado.
// Solo pueden borrar ofertas los perfiles 1 (admin) y 3 (ofertante).
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_SESSION['usuario'])
    && ($_SESSION['usuario']['perfil_id'] == 1 || $_SESSION['usuario']['perfil_id'] == 3)
) {

    // Conexión a la base de datos.
    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $oferta_id = $_POST['oferta_id'];

    // Si el usuario es perfil 3 (ofertante), comprobamos que la oferta realmente le pertenece.
    if ($_SESSION['usuario']['perfil_id'] == 3) {
        $query_verificar = "SELECT usuario_id FROM ofertas WHERE id = :oferta_id";
        $stmt_verificar = $conexion->prepare($query_verificar);
        $stmt_verificar->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
        $stmt_verificar->execute();
        $oferta = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

        // Si el usuario no es el dueño de esa oferta, lo devolvemos con un error.
        if ($oferta['usuario_id'] != $_SESSION['usuario']['id']) {
            header('Location: index.php?error=No+puedes+borrar+esta+oferta');
            exit;
        }
    }

    // Eliminamos la oferta de la base de datos.
    $query = "DELETE FROM ofertas WHERE id = :oferta_id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigimos con un mensaje de éxito.
    header('Location: index.php?mensaje=Oferta+borrada+con+éxito');
    exit;
} else {
    // Si se accede de forma no permitida, volvemos al inicio.
    header('Location: index.php');
    exit;
}
