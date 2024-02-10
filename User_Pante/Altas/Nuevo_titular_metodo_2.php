<?php
include("../../conexiones/conexion.php");

// Desactiva la visualización de errores en la pantalla
ini_set('display_errors', 0);
// Asegúrate de que los errores se escriban en el archivo de log de errores de PHP
ini_set('log_errors', 1);

session_start();
$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$Nombre_titular = $_POST['Nombre_titular'];
$Apellido_Materno = $_POST['Apellido_Materno'];
$Apellido_Paterno = $_POST['Apellido_Paterno'];
$Telefono_titular = $_POST['Telefono_titular'];
$telefono_casa_metodo_2 = $_POST['telefono_casa_metodo_2'];

date_default_timezone_set('America/Mexico_City');
$fecha_captura = date('Y-m-d'); // Obtén la fecha actual

if (isset($_POST['Direccion'])) {
    $Direccion = $_POST['Direccion'];
} else {
    $Direccion = $_POST['Agregar_manualmente_editar_check_2'];
}



try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "INSERT INTO `mxpt_titulares`(`Nombre_titular`, `Apellido_Paterno_Titular`, `Apellido_Materno_Titular`, `Telefono_titular`, `Telefono_Casa`, `Direccion`, `Responsable_cap`, `Fecha_Captura_Titular`)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("ssssssss", $Nombre_titular, $Apellido_Paterno, $Apellido_Materno, $Telefono_titular, $telefono_casa_metodo_2, $Direccion, $responsable_cap, $fecha_captura);

    // Ej
    // Ejecuta la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente

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

$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
