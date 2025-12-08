<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Verificar que se envió el formulario por POST y que el usuario tiene perfil de Gestor (2) o Administrador (1)
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario']) &&
    ($_SESSION['usuario']['perfil_id'] == 2 || $_SESSION['usuario']['perfil_id'] == 1)
) {

    $conexion = conectarPDO($host, $user, $password, $bbdd); // Conectar a la base de datos
    $oferta_id = $_POST['oferta_id']; // ID de la oferta a visar/desvisar
    $accion = $_POST['accion']; // Acción a realizar: 'visar' o 'desvisar'

    // Preparar la consulta según la acción
    if ($accion == 'visar') {
        $query = "UPDATE ofertas SET visada = 1 WHERE id = :oferta_id"; // Marcar como visada
    } elseif ($accion == 'desvisar') {
        $query = "UPDATE ofertas SET visada = 0 WHERE id = :oferta_id"; // Desmarcar como visada
    }

    // Ejecutar la actualización
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':oferta_id', $oferta_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigir al inicio después de la acción
    header('Location: index.php');
    exit;
} else {
    // Si no se cumplen las condiciones, redirigir al inicio
    header('Location: index.php');
    exit;
}
