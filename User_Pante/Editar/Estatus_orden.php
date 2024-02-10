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
    // Obtener los datos del formulario AJAX
    $Estatus = $_POST['Estatus'];
    $id_pago = $_POST['id_pago'];
    $Folio = $_POST['Folio'];

    // Verificar si el folio de tesorería ya existe en otras filas
    $query_check_folio = "SELECT * FROM `mxpt_pagos` WHERE `Folio_Tesoreria`=? AND `id_pago` != ?";
    $stmt_check_folio = $mysqli->prepare($query_check_folio);
    if (!$stmt_check_folio) {
        throw new Exception("Error en la preparación de la sentencia: " . $mysqli->error);
    }
    $stmt_check_folio->bind_param("si", $Folio, $id_pago);
    $stmt_check_folio->execute();
    $result_check_folio = $stmt_check_folio->get_result();

    if ($result_check_folio->num_rows > 0) {
        echo 'Folio_uso'; // Si el folio ya existe, devuelve 'Folio_uso' al cliente
        exit();
    }
    $stmt_check_folio->close();

    date_default_timezone_set('America/Mexico_City'); // Establece la zona horaria a México
    $Fecha_acreditacion = date('Y-m-d H:i:s'); // Añade la hora también

    // Prepara la consulta SQL con marcadores de posición
    $query = "UPDATE `mxpt_pagos` SET `Folio_Tesoreria`=?, `Estatus`=? , `Fecha_acreditacion`=? , `Responsable_cap`=? WHERE `id_pago`=?";

    // Prepara la sentencia SQL
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la sentencia: " . $mysqli->error);
    }

    // Vincular los parámetros a los marcadores de posición
    $stmt->bind_param("ssssi", $Folio, $Estatus, $Fecha_acreditacion, $responsable_cap, $id_pago);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la sentencia: " . $stmt->error);
    } else {
        echo 'exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    
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
    $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
?>