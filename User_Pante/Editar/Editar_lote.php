<?php
include("../../conexiones/conexion.php");
session_start();

// Desactiva la visualización de errores en la pantalla
ini_set('display_errors', 0);
// Asegúrate de que los errores se escriban en el archivo de log de errores de PHP
ini_set('log_errors', 1);

$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtener los datos del formulario AJAX

$idLote = $_POST['idLote'];
$idPanteon = $_POST['idPanteon'];
$nombreLote = $_POST['nombreLote'];

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "UPDATE catalogo_lotes SET Lote=?, id_panteon=?, Responsable_cap=?, Fecha_Captura_lote=NOW() WHERE id_lote=?";

    // Prepara la sentencia SQL
    $stmt = $mysqli->prepare($query);

    // Vincular los parámetros a los marcadores de posición
    $stmt->bind_param("sisi", $nombreLote, $idPanteon, $responsable_cap, $idLote);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente
            
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Success: Data updated successfully";
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
    $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
?>
