<?php
session_start();

// Desactiva la visualización de errores en la pantalla
ini_set('display_errors', 0);
// Asegúrate de que los errores se escriban en el archivo de log de errores de PHP
ini_set('log_errors', 1);


include("../../conexiones/conexion.php");
$username = $_SESSION['user_login_panteones'];

$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$id_usuario = $dat['id_usuario'];

$id_panteon = $_POST['id'];
$Panteon = $_POST['panteon'];
$Descripcion = $_POST['Descripcion'];

date_default_timezone_set('America/Mexico_City');
$fecha_actual = date("Y-m-d H:i:s");

try {
    // Primero, verifica si ya existe un registro con los mismos datos
    $query_check = "SELECT Panteon , id_panteon FROM `mxpt_panteones` WHERE `Panteon`=? AND `id_panteon`!=?";
    $stmt_check = $mysqli->prepare($query_check);
    $stmt_check->bind_param("si", $Panteon, $id_panteon);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo 'existe'; // Si ya existe un registro con los mismos datos, devuelve 'existe'
        exit();
    }

    $stmt_check->close();

    // Crea la sentencia SQL para actualizar
    $query = "UPDATE `mxpt_panteones` SET `Panteon`=?, `Descripcion`=? , `Responsable_cap`=? , `Fecha_Captura_Panteon`=? WHERE `id_panteon`=?;";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        throw new Exception("Error en la preparación: " . $mysqli->error);
    }

    // Vincular los parámetros a los marcadores de posición
    $stmt->bind_param("ssssi", $Panteon, $Descripcion, $id_usuario, $fecha_actual, $id_panteon);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
        // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $id_usuario - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";
    
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
    $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $id_usuario - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
?>
