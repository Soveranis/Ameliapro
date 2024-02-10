<?php
include("../../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_tecno'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

try {
    if (isset($_POST['estado_modulo_Tumbas'])) {

        $Tumbas = $_POST['estado_modulo_Tumbas'];
        $Pagos = $_POST['estado_modulo_Pagos'];
        $Reporte_General = $_POST['Reportes_Generales'];
        $Servicios = $_POST['estado_modulo_Reporte_Servicios'];
        $Panteones = $_POST['estado_modulo_Panteones'];

        $query = "UPDATE `catalogo_modulos` SET Tumbas=?, Pagos=?, Reporte_General=?, Servicios=?, Panteones=? ";
        
        // Prepara la sentencia SQL
        $stmt = $mysqli->prepare($query);

        // Vincula los parámetros a los marcadores de posición
        $stmt->bind_param("sssss", $Tumbas, $Pagos, $Reporte_General, $Servicios, $Panteones);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            echo 'exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente

            // Registro de eventos exitosos
            date_default_timezone_set('America/Mexico_City');
            $logFileName = "log_" . date("Y-m-d") . ".txt";
            $logFilePath = "../../event_logs/logs/" . $logFileName;
            $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Success: Data updated successfully";
            if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                error_log("Error al escribir en el archivo de log.");
            }
        } else {
            echo 'error';
            throw new Exception($stmt->error);
        }
    } else {
        echo 'error';
        // Registro de eventos de error
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
    
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: Datos no enviados correctamente";
        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    }
} catch (Exception $e) {
    echo 'error';
    // Registro de eventos de error
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
