<?php
session_start();
require_once("funciones.php");
require_once("variables.php");

// Solo un administrador (perfil 1) puede crear nuevos usuarios o gestores.
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil_id'] == 1) {

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    // Procesamos el formulario únicamente cuando llega por POST.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        // Encriptamos la contraseña para guardarla de forma segura.
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $perfil_id = $_POST['perfil_id'];
        $activo = 1; // Los usuarios creados desde administración se activan directamente.
        $token = bin2hex(random_bytes(30)); // Token único para el usuario.
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;

        // Si el perfil elegido es 2, lo guardamos en la tabla de gestores.
        // Para el resto de perfiles, lo insertamos en la tabla general de usuarios.
        if ($perfil_id == 2) {
            $query = "INSERT INTO gestores 
                      (nombre, email, password, perfil_id, created_at, updated_at) 
                      VALUES (:nombre, :email, :password, :perfil_id, :created_at, :updated_at)";
        } else {
            $query = "INSERT INTO usuarios 
                      (nombre, email, password, perfil_id, activo, token, created_at, updated_at) 
                      VALUES (:nombre, :email, :password, :perfil_id, :activo, :token, :created_at, :updated_at)";
        }

        $stmt = $conexion->prepare($query);

        // Parámetros comunes para ambas tablas.
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);

        // Solo añadimos activo y token cuando se inserta en la tabla usuarios.
        if ($perfil_id != 2) {
            $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        }

        try {
            // Guardamos el nuevo registro en la base de datos.
            $stmt->execute();
            header('Location: lista_usuarios.php');
            exit;
        } catch (PDOException $e) {
            // Mostramos cualquier error que ocurra durante la ejecución.
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    // Si no es administrador, se le redirige fuera.
    header('Location: index.php');
    exit;
}
