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
$id_finado = $_POST['finado'];
$id_titular_nuevo = $_POST['titular_nuevo'];
$id_titular_actual = $_POST['id_titular_actual'];
$fecha_actual = date("Y-m-d H:i:s");

try {
    // Insertar el titular actual en la tabla de historial
    $query_insert = "INSERT INTO `catalogo_historico_titulares`(`id_titular`, `id_finado`, `Responsable_Cambio`, `Fecha_Cambio`) VALUES (?, ?, ?, ? )";
    $stmt_insert = $mysqli->prepare($query_insert);
    if ($stmt_insert) {
        $stmt_insert->bind_param("iiss", $id_titular_actual, $id_finado, $responsable_cap, $fecha_actual);
        $stmt_insert->execute();
        $stmt_insert->close();
    } else {
        throw new Exception("Error en la inserción");
    }

    $query_update = "UPDATE `mxpt_finados` SET `Titular` = ?, `Responsable_cap` = ? WHERE `id_finado` = ?";
    $stmt_update = $mysqli->prepare($query_update);
    if ($stmt_update) {
        $stmt_update->bind_param("iii", $id_titular_nuevo, $responsable_cap, $id_finado);
        if ($stmt_update->execute()) {
            echo 'exito_cambio_titular';
            date_default_timezone_set('America/Mexico_City');
            $logFileName = "log_" . date("Y-m-d") . ".txt";
            $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
        
            // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
            $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";
        
            if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                error_log("Error al escribir en el archivo de log.");
            }
        } else {
            throw new Exception("Error al actualizar: " . $stmt_update->error);
        }
        $stmt_update->close();
    } else {
        throw new Exception("Error en la preparación de la sentencia de actualización");
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

// Cerrar la conexión a la base de datos
$mysqli->close();
?>