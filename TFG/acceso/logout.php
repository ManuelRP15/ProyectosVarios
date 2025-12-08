<?php
session_start(); // Inicia la sesión actual para poder manipularla
session_destroy(); // Destruye la sesión, eliminando todos los datos de $_SESSION (logout)
header('Location: ../index.php'); // Redirige al usuario a la página principal después de cerrar sesión
exit; // Detiene la ejecución del script para asegurar que no se ejecute código adicional
