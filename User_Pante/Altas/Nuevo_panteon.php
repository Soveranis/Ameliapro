<?php
session_start();
include("../../conexiones/conexion.php");
$username = $_SESSION['user_login_panteones'];

$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$id_usuario = $dat['id_usuario'];

// Recoge los datos enviados desde AJAX
$panteon = $_POST['panteon'];
$descripcion = $_POST['Descripcion'];
$responsable_cap = $id_usuario;

date_default_timezone_set('America/Mexico_City');
$fecha_captura = date("Y-m-d\TH:i");

try {
    // Verifica si el panteón ya existe
    $checkQuery = "SELECT Panteon FROM `mxpt_panteones` WHERE `Panteon` = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("s", $panteon);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo 'existe'; // Si el panteón ya existe, devuelve 'existe' al cliente
        exit();
    }

    $checkStmt->close();

    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `mxpt_panteones`(`Panteon`, `Descripcion`, `Responsable_cap`, `Fecha_Captura_Panteon`) VALUES (?, ?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        throw new Exception("Error en la preparación: " . $mysqli->error);
    }

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("ssis", $panteon, $descripcion, $responsable_cap, $fecha_captura);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        
        echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
    
        // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
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

$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
?>