<?php
include("../../conexiones/conexion.php");
session_start();

$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$id_titular = $_POST['id_titular'];
$nombre_finado = $_POST['Nombre_finado'];
$Apellido_Paterno = $_POST['Apellido_Paterno'];
$Apellido_Materno = $_POST['Apellido_Materno'];
$fecha_inhumacion = $_POST['Fecha_inhumacion'];

date_default_timezone_set('America/Mexico_City');
$fecha_captura = date('Y-m-d'); // Obtén la fecha actual

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `mxpt_finados`(`Titular`, `Nombre_finado`, `Apellido_Paterno_Finado`, `Apellido_Materno_Finado`, `Responsable_cap`, `Fecha_inhumacion`,`Fecha_Captura_Finado`) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("sssssss", $id_titular, $nombre_finado, $Apellido_Paterno, $Apellido_Materno, $responsable_cap, $fecha_inhumacion, $fecha_captura);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        echo 'exito';
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
        // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";

        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
    echo 'error';
}


$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
