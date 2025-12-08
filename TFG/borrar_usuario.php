<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Solo permitimos acceder a este proceso si el usuario es administrador (perfil 1).
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1) {

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    // Solo actuamos si se ha enviado el formulario.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $usuario_id = $_POST['usuario_id'];
        $gestor_id = $_POST['gestor_id'];

        try {
            // Comprobamos si el usuario que llega es realmente un gestor.
            $queryGestor = "SELECT * FROM gestores WHERE id = :gestor_id";
            $stmtGestor = $conexion->prepare($queryGestor);
            $stmtGestor->bindParam(':gestor_id', $gestor_id, PDO::PARAM_INT);
            $stmtGestor->execute();
            $gestor = $stmtGestor->fetch(PDO::FETCH_ASSOC);

            if ($gestor) {
                // Si es un gestor, lo eliminamos directamente de su tabla.
                $queryDeleteGestor = "DELETE FROM gestores WHERE id = :gestor_id";
                $stmtDeleteGestor = $conexion->prepare($queryDeleteGestor);
                $stmtDeleteGestor->bindParam(':gestor_id', $gestor_id, PDO::PARAM_INT);
                $stmtDeleteGestor->execute();
            } else {
                // Si no es gestor, primero borramos las ofertas que tenga asociadas.
                $queryOfertas = "DELETE FROM ofertas WHERE usuario_id = :usuario_id";
                $stmtOfertas = $conexion->prepare($queryOfertas);
                $stmtOfertas->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmtOfertas->execute();

                // Y finalmente borramos al usuario.
                $queryUsuario = "DELETE FROM usuarios WHERE id = :usuario_id";
                $stmtUsuario = $conexion->prepare($queryUsuario);
                $stmtUsuario->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmtUsuario->execute();
            }

            // Volvemos a la lista de usuarios cuando termina todo.
            header('Location: lista_usuarios.php');
            exit;
        } catch (PDOException $e) {
            // En caso de error mostramos el mensaje.
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    // Si alguien intenta entrar sin permisos, lo mandamos al inicio.
    header('Location: index.php');
    exit;
}
