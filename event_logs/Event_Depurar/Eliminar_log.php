<?php
// Configurar la zona horaria de México
date_default_timezone_set('America/Mexico_City');

// Consultar el valor actual de la base de datos
$query_dupurar = "SELECT Dias_Depuracion_Log, Fecha_Depuracion FROM catalogo_configuracion";
$result = $mysqli->query($query_dupurar);

if ($result) {
    $row = $result->fetch_assoc();
    $valorActual = $row['Dias_Depuracion_Log'];
    $fechaDepuracion = $row['Fecha_Depuracion'];

    // Obtener la fecha actual
    $fechaActual = date('Y-m-d');

    // Si la fecha de depuración es menor o igual a la fecha actual, borra los archivos de log
    if (strtotime($fechaDepuracion) <= strtotime($fechaActual)) {
        // Ruta de los archivos de log
        $directorioLogs = 'event_logs/Logs/';

        // Extensión de archivo a buscar (en este caso, archivos txt)
        $extensionArchivo = 'txt';

        // Obtener la lista de archivos en el directorio de log
        $archivos = scandir($directorioLogs);

// Recorrer la lista de archivos y borrar todos
foreach ($archivos as $archivo) {
    if ($archivo != '.' && $archivo != '..' && pathinfo($archivo, PATHINFO_EXTENSION) == $extensionArchivo) {
        $rutaArchivo = $directorioLogs . $archivo;
        if (unlink($rutaArchivo)) {
            // Realizar la consulta para obtener la fecha actual de Fecha_Depuracion
            $query_obtener_fecha = "SELECT Fecha_Depuracion, Dias_Depuracion_Log FROM catalogo_configuracion";
            $result_obtener_fecha = $mysqli->query($query_obtener_fecha);

            if ($result_obtener_fecha) {
                $row_fecha = $result_obtener_fecha->fetch_assoc();
                $fechaActual = $row_fecha['Fecha_Depuracion'];
                $Nr_Dias = $row_fecha['Dias_Depuracion_Log'];

                // Verifica si Fecha_Depuracion es una fecha válida
                if (strtotime($fechaActual) !== false) {
                    date_default_timezone_set('America/Mexico_City');
                    // Calcular la nueva fecha de depuración sumando $Nr_Dias días
                    $fechaDepuracion = date('Y-m-d', strtotime($fechaActual . " + $Nr_Dias days"));

                    // Actualizar la base de datos con la nueva fecha de depuración
                    $query_actualizar_fecha = "UPDATE catalogo_configuracion SET Fecha_Depuracion=?";
                    $stmt_actualizar_fecha = $mysqli->prepare($query_actualizar_fecha);
                    $stmt_actualizar_fecha->bind_param("s", $fechaDepuracion);
                    $stmt_actualizar_fecha->execute();
                } else {
                    echo 'Fecha de depuración no es válida.<br>';
                }
            } else {
                echo 'Error al obtener la fecha de depuración desde la base de datos.<br>';
            }
        } else {
            echo "Error al eliminar el archivo $archivo.<br>";
        }
    }
}



// Crear un nuevo archivo de log
$logFileName = "log_" . date("Y-m-d") . ".txt";
$logFilePath = "../logs/" . $logFileName;
$logMessage = date("Y-m-d H:i:s") . " - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Message: Registros eliminados.";
if (file_put_contents($logFilePath, $logMessage . "\n") === false) {
    error_log("Error al escribir en el archivo de log.");
}

    }
} else {
    echo "Error al obtener el valor de depuración desde la base de datos.";
}
?>