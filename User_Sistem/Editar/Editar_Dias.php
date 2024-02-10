<?php
include("../../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_sistemas'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

try {
    if (isset($_POST['Nr_Dias'])) {

        $Nr_Dias = $_POST['Nr_Dias'];
        date_default_timezone_set('America/Mexico_City');
        // Obtener la fecha actual
        $fechaActual = date('Y-m-d');

        // Calcular la nueva fecha de depuración sumando $Nr_Dias días
        $fechaDepuracion = date('Y-m-d', strtotime($fechaActual . " + $Nr_Dias days"));

        $query = "UPDATE catalogo_configuracion SET Dias_Depuracion_Log=?, Fecha_Depuracion=? ";


        // Prepara la sentencia SQL
        $stmt = $mysqli->prepare($query);

        // Vincula los parámetros a los marcadores de posición
        $stmt->bind_param("ss", $Nr_Dias, $fechaDepuracion);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            echo 'Exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente

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
