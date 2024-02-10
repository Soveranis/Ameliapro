<?php
include("../conexiones/conexion.php");
session_start();

// Obtener el nombre de usuario de la sesión
$username = $_SESSION['user_login_panteones'];

// Registro de log al cerrar sesión
date_default_timezone_set('America/Mexico_City');
$logFileName = "log_" . date("Y-m-d") . ".txt";
$logFilePath = "../../event_logs/logs/" . $logFileName;
$logMessage = date("Y-m-d H:i:s") . " - User: $username - Message: Cierre de sesión exitoso";

if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
    error_log("Error al escribir en el archivo de log al cerrar sesión.");
}

// Destruir solo la sesión del usuario actual
unset($_SESSION['user_login_panteones']);
session_destroy(); // Destruye la sesión actual

// Redirigir a la página de inicio de sesión
header('Location: ../../index.php');
?>
