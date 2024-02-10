<?php
// Desactiva la visualización de errores en la pantalla
ini_set('display_errors', 0);
// Asegúrate de que los errores se escriban en el archivo de log de errores de PHP
ini_set('log_errors', 1);

include("../../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

try {
    // Obtén los datos del formulario
    $id_pago = $_POST['id_pago'];

    date_default_timezone_set('America/Mexico_City');
    $fecha_captura = date("Y-m-d\TH:i");

    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `catalogo_historico_pagos`(`Orden_pago`, `Responsable_Cambio`, `Fecha_Respaldo`) VALUES (?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la sentencia: " . $mysqli->error);
    }

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("sss", $id_pago, $responsable_cap, $fecha_captura);

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la sentencia: " . $stmt->error);
    } else {
        echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
    
        // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";
    
        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    }

    $stmt->close();

} catch (Exception $e) {
    echo 'error';

    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
?>
