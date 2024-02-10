<?php
include("../../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_panteones'];

$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$id_servicio = $_POST['Servicio'];
$id_tumba = $_POST['tumba'];
$Monto_Pago = $_POST['Monto'];
$Estatus = 'Pendiente';
$folio_Panteones = $_POST['folio'];
date_default_timezone_set('America/Mexico_City');
$Fecha_Captura_Pago = date('Y-m-d H:i:s');

$Inser = "SELECT Fecha_Limite_Pago FROM catalogo_configuracion";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$Tiempo_Limite = (int) $dat['Fecha_Limite_Pago'];

$Fecha_Limite_Pago = date('Y-m-d H:i:s', strtotime($Fecha_Captura_Pago . " +$Tiempo_Limite days"));

try {
    $query = "INSERT INTO `mxpt_pagos`(`id_servicio`, `id_tumba`, `Folio_Panteones`, `Monto_Pago`, `Estatus`, `Responsable_cap`, `Fecha_Captura_Pago`, `Fecha_Limite_Pago`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación: " . $mysqli->error);
    }

    $stmt->bind_param("ssssssss", $id_servicio, $id_tumba, $folio_Panteones, $Monto_Pago, $Estatus, $responsable_cap, $Fecha_Captura_Pago, $Fecha_Limite_Pago);

    if ($stmt->execute()) {
        echo 'exito';

        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";

        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    } else {
        throw new Exception("Error en la ejecución: " . $stmt->error);
    }

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

$stmt->close();
$mysqli->close();
?>