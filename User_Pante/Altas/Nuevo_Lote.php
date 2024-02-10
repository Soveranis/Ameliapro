<?php
session_start();
include("../../conexiones/conexion.php");

$username = $_SESSION['user_login_panteones'];

$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$id_usuario = $dat['id_usuario'];

// Recoge los datos enviados desde AJAX
$Nombre_lote = $_POST['Nombre_lote'];
$id_panteon = $_POST['Panteon'];
$responsable_cap = $id_usuario;

date_default_timezone_set('America/Mexico_City');
$fecha_captura = date("Y-m-d\TH:i");

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `catalogo_lotes`(`id_panteon`, `Lote`, `Responsable_cap`, `Fecha_Captura_lote`) VALUES (?, ?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        throw new Exception("Error en la preparación: " . $mysqli->error);
    }

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("ssis", $id_panteon, $Nombre_lote, $responsable_cap, $fecha_captura);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente

        // Registro en el log
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
        // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Mensaje: Registro exitoso";

        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    } else {
        throw new Exception("Error en la ejecución: " . $stmt->error);
    }
} catch (Exception $e) {
    echo 'error';

    // Registro de errores
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}
$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
?>
