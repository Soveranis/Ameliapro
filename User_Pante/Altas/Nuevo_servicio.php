<?php
// Desactiva la visualización de errores
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

session_start();
include("../../conexiones/conexion.php");
$username = $_SESSION['user_login_panteones'];

$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable = $dat['id_usuario'];

try {
    // Obtén los valores enviados por AJAX
    $nombre_Servicio = $_POST['nombre_Servicio'];
    $descrip_Servicio = $_POST['descrip_Servicio'];
    $Monto = $_POST['Monto'];
    date_default_timezone_set('America/Mexico_City'); // Establece la zona horaria a México
    $fecha_captura = date('Y-m-d');

    // Verifica si los campos están vacíos
    if (empty($nombre_Servicio) || empty($descrip_Servicio) || empty($Monto)) {
        throw new Exception("Campos vacíos");
    }

    // Verifica si el servicio ya existe
    $consulta = "SELECT * FROM mxpt_servicios WHERE Nombre_Servicio = ?";
    $stmt_verificar = $mysqli->prepare($consulta);
    $stmt_verificar->bind_param("s", $nombre_Servicio);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows > 0) {
        echo 'existe'; // Devuelve 'existe' al cliente si el servicio ya existe
    } else {
        // Prepara la consulta SQL con marcadores de posición
        $query = "INSERT INTO mxpt_servicios (Nombre_Servicio, Descripcion, Monto, Responsable_cap, Fecha_Captura_Servicio) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            throw new Exception("Error en la preparación: " . $mysqli->error);
        }

        // Vincula los valores a los marcadores de posición
        $stmt->bind_param("ssdss", $nombre_Servicio, $descrip_Servicio, $Monto, $responsable, $fecha_captura);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente
            date_default_timezone_set('America/Mexico_City');
            $logFileName = "log_" . date("Y-m-d") . ".txt";
            $logFilePath = "../../event_logs/logs/" . $logFileName;
            // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
            $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";
        
            if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                error_log("Error al escribir en el archivo de log.");
            }
        } else {
            throw new Exception("Error en la ejecución: " . $stmt->error);
        }

        $stmt->close();
    }

    $stmt_verificar->close();

} catch (Exception $e) {
    echo 'error';
    
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
?>