<?php
// XAMPP
$host = "localhost";       // MySQL local
$user = "root";            // Usuario por defecto de XAMPP
$password = "";            // Contraseña por defecto de XAMPP
$bbdd = "plataformadb";    // BBDD en phpMyAdmin

// Codigos HTTP
$headerJSON = 'Content-Type: application/json';
$codigosHTTP = [
    "200" => "HTTP/1.1 200 OK",
    "201" => "HTTP/1.1 201 Created",
    "202" => "HTTP/1.1 202 Accepted",
    "400" => "HTTP/1.1 400 Bad Request",
    "404" => "HTTP/1.1 404 Not Found",
    "500" => "HTTP/1.1 500 Internal Server Error"
];
