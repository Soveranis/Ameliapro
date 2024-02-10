<?php
include("../../conexiones/conexion.php");
session_start();

$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$Numero_tumba = 'Nr-1';
$id_finado = $_POST['id_finado'];
if (empty($id_finado)) {
    echo "Ingrese un finado";
    exit; // Termina la ejecución del script
}
$selectPanteon = $_POST['selectPanteon'];
$selectLote = $_POST['selectLote'];
$Tipo_Tumba_Metodo_1 = $_POST['Tipo_Tumba_Metodo_1'];
$Medidas_Nueva_tumba_1 = $_POST['Medidas_Nueva_tumba_1'];
$Fecha_Inhumacion_1 = $_POST['Fecha_Inhumacion_1'];

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `mxpt_tumbas`(`Nr_tumba`, `Finado_tum`, `Panteon_Tum`, `Tipo_Tumba_tum`, `Lote_tum`, `Medidas_tum`, `Responsable_cap_tum`, `Fecha_apertura_tum`) VALUES ( ? , ? , ? , ? , ? , ? , ? , ?)";

    
    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssssssi", $Numero_tumba, $id_finado, $selectPanteon, $Tipo_Tumba_Metodo_1, $selectLote, $Medidas_Nueva_tumba_1, $responsable_cap, $Fecha_Inhumacion_1);


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



?>