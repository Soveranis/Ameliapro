<?php
include("../../conexiones/conexion.php");
session_start();


$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$Nombre_titular = $_POST['Nombre_titular'];
$Apellido_Paterno = $_POST['Apellido_Paterno'];
$Apellido_Materno = $_POST['Apellido_Materno'];
$Telefono_titular = $_POST['Telefono_titular'];
$Telefono_casa = $_POST['Telefono_casa'];



if (isset($_POST['Direccion'])) {
    $Direccion = $_POST['Direccion'];
} else {
    $Direccion = $_POST['Ingresar_Manualmente'];
}

date_default_timezone_set('America/Mexico_City');
$fecha_captura = date('Y-m-d'); // Obtiene la fecha actual en la zona horaria de México


try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `mxpt_titulares`(`Nombre_titular`, `Apellido_Paterno_Titular`, `Apellido_Materno_Titular`, `Telefono_titular`, `Telefono_Casa`, `Direccion`, `Responsable_cap`, `Fecha_Captura_Titular`)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("ssssssss", $Nombre_titular, $Apellido_Paterno, $Apellido_Materno, $Telefono_titular, $Telefono_casa, $Direccion, $responsable_cap, $fecha_captura);

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
    echo 'error';
    date_default_timezone_set('America/Mexico_City');
$logFileName = "log_" . date("Y-m-d") . ".txt";
$logFilePath = "../../event_logs/logs/" . $logFileName;
// Incluye solo el nombre del archivo y el número de línea en el mensaje de log
$logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
    error_log("Error al escribir en el archivo de log.");
}
}
