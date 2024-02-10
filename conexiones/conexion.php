<?php

// // Configuración de la base de datos para entorno de desarrollo
// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');
// define('DB_NAME', 'panteones');


// // Configuración de la base de datos para entorno de desarrollo
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'panteones');


// Crear una instancia de la clase mysqli para establecer la conexión
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($mysqli->connect_error) {
    // Redirigir a la página de error 404
    header("Location: Vistas/Error_404.php");
    exit();
}

// Establecer el conjunto de caracteres UTF-8 para evitar problemas de codificación
if (!$mysqli->set_charset("utf8")) {
    die("ERROR: Error al configurar el conjunto de caracteres UTF-8: " . $mysqli->error);
}


?>
