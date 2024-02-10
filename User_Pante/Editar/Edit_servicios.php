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
$id_usuario = $dat['id_usuario'];

try {
    // Obtén los valores enviados por AJAX
    $id_servicio = $_POST['id'];
    $nombre_servicio = $_POST['nombre_servicio'];
    $descripcion_servicio = $_POST['descripcion_servicio'];
    $monto = $_POST['monto'];

    if (empty($id_servicio) || empty($nombre_servicio) || empty($descripcion_servicio) || empty($monto)) {
        throw new Exception("Campos vacíos");
    }

    // Verifica si el servicio ya existe (excluyendo el propio servicio que se está editando)
    $consulta = "SELECT * FROM mxpt_servicios WHERE Nombre_Servicio = ? AND id_servicio != ?";
    $stmt_verificar = $mysqli->prepare($consulta);
    $stmt_verificar->bind_param("si", $nombre_servicio, $id_servicio);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows > 0) {
        echo 'existe'; // Devuelve 'existe' al cliente si el servicio ya existe
    } else {
        // Obtén los datos actuales del servicio
        $consulta_actual = "SELECT `id_servicio`, `Nombre_Servicio`, `Descripcion`, `Monto`, `Responsable_cap`, `Fecha_Captura_Servicio` FROM `mxpt_servicios` WHERE id_servicio = ?";
        $stmt_actual = $mysqli->prepare($consulta_actual);
        $stmt_actual->bind_param("i", $id_servicio);
        $stmt_actual->execute();
        $resultado_actual = $stmt_actual->get_result();
        $datos_actuales = $resultado_actual->fetch_assoc();

        // Inserta los datos actuales en la tabla de historial
        $query_historial = "INSERT INTO catalogo_historico_servicios (Nombre_Servicio_cambio, Descripcion_cambio, Monto_cambio, Responsable_cambio, Fecha_cambio) VALUES (?, ?, ?, ?, ?)";
        $stmt_historial = $mysqli->prepare($query_historial);
        $fecha_actual = date("Y-m-d H:i:s");
        $stmt_historial->bind_param("sssss", $datos_actuales['Nombre_Servicio'], $datos_actuales['Descripcion'], $datos_actuales['Monto'], $id_usuario, $datos_actuales['Fecha_Captura_Servicio']);

        $stmt_historial->execute(); 
        if ($stmt_historial->error) {
            throw new Exception("Error al insertar en la tabla de historial: " . $stmt_historial->error);
        }

        // Prepara la consulta SQL con marcadores de posición
        $query = "UPDATE mxpt_servicios SET Nombre_Servicio = ?, Descripcion = ?, Monto = ?, Responsable_cap = ?, Fecha_Captura_Servicio = ? WHERE id_servicio = ?";

        // Prepara la sentencia SQL
        $stmt = $mysqli->prepare($query);

        // Verifica si la preparación de la consulta fue exitosa
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta");
        }

        // Vincula los valores a los marcadores de posición
        $stmt->bind_param("sssssi", $nombre_servicio, $descripcion_servicio, $monto, $id_usuario, $fecha_actual, $id_servicio);

        // Ejecuta la consulta
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        } else {
            echo 'exito'; // Si la actualización fue exitosa, devuelve 'exito' al cliente
            date_default_timezone_set('America/Mexico_City');
            $logFileName = "log_" . date("Y-m-d") . ".txt";
            $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
        
            // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
            $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registro exitoso";
        
            if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                error_log("Error al escribir en el archivo de log.");
            }
        }

        // Cierra la sentencia
        $stmt->close();
    }

    // Cierra el statement y la conexión
    $stmt_verificar->close();

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