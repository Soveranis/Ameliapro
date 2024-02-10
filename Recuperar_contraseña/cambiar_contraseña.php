<?php
include("../conexiones/conexion.php");

try {
    // Obtener datos del formulario
    $pass1 = $_POST["pass1"];
    $correo = $_POST["correo"];

    // Validar los datos de entrada si es necesario

    // Preparar la consulta para obtener el id_usuario
    $stmt = $mysqli->prepare("SELECT id_usuario FROM mxpt_usuarios WHERE Mail = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $id_usuario = $fila['id_usuario'];



    // Preparar la consulta para actualizar la contraseña
    $stmt = $mysqli->prepare("UPDATE mxpt_usuarios SET Password=? WHERE id_usuario = ?");
    $stmt->bind_param("si", $pass1, $id_usuario);
    
    if ($stmt->execute()) {
        // Si la actualización fue exitosa, devolver 'ok' como respuesta
        echo 'ok';

        // Generar registro de log de éxito
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $id_usuario - File: " . basename(__FILE__) . " - Password updated successfully";
        
        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log al actualizar la contraseña.");
        }
    } else {
        // Si hubo un error, lanzar una excepción
        throw new Exception('Error al actualizar la contraseña: ' . $stmt->error);
    }
} catch (Exception $e) {
    // Manejar excepción y generar registro de log de error
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $id_usuario - File: " . basename(__FILE__) . " - Error: " . $e->getMessage();
    
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
    
    echo 'Error al actualizar la contraseña: ' . $e->getMessage();
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>