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
$id_finado = $_POST['id_finado'];
$nombre_finado = $_POST['nombre_finado'];
$ApellidoMaternoFINADO = $_POST['ApellidoMaternoFINADO'];
$ApellidoPaternoFINADO = $_POST['ApellidoPaternoFINADO'];
$fecha_inhumacion = $_POST['fecha_inhumacion'];

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "UPDATE `mxpt_finados` SET `Nombre_finado` = ?, `Apellido_Paterno_Finado` = ?, `Apellido_Materno_Finado` = ?, `Fecha_inhumacion` = ?, `Responsable_cap` = ?  WHERE `id_finado` = ?";

    // Prepara la sentencia SQL
    $stmt = $mysqli->prepare($query);

    // Vincular los parámetros a los marcadores de posición
    $stmt->bind_param("sssssi", $nombre_finado, $ApellidoPaternoFINADO, $ApellidoMaternoFINADO, $fecha_inhumacion, $responsable_cap, $id_finado);

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
