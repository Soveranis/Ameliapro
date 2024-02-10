<?php
include("../../conexiones/conexion.php");
session_start();


$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT usuario, id_usuario FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];
// Verificar si se envió el id_titular y no está vacío
if (!isset($_POST['id_titular_editar']) || empty($_POST['id_titular_editar'])) {
    // Registrar el error en el log
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: id_titular no enviado o vacío";
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
    // Detener la ejecución del script y enviar una respuesta de error al cliente
    echo 'error';
    exit;
}

$id_titular_editar = $_POST['id_titular_editar'];
$Nombre_titular_editar = $_POST['Nombre_titular_editar'];
$Apellido_Paterno_Editar = $_POST['Apellido_Paterno_editar'];
$Apellido_Materno_Editar = $_POST['Apellido_Materno_Editar'];
$Telefono_titular_editar = $_POST['Telefono_titular_editar'];
$telefono_casa_edita = $_POST['telefono_casa_edita'];


if (isset($_POST['Direccion'])) {
    $Direccion = $_POST['Direccion'];
} elseif (isset($_POST['checkDireccion'])) {
    $Direccion = $_POST['checkDireccion'];
} elseif (isset($_POST['Agregar_manualmente_editar_check'])) {
    $Direccion = $_POST['Agregar_manualmente_editar_check'];
} else {
    // Manejar el caso en el que ninguna de las variables esté presente
    // Puedes asignar un valor predeterminado o mostrar un mensaje de error
    $Direccion = "Valor predeterminado o mensaje de error";
}

// ...

try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "UPDATE `mxpt_titulares` SET `Nombre_titular`=?,`Apellido_Paterno_Titular`=?,`Apellido_Materno_Titular`=?,`Telefono_titular`=?,`Telefono_Casa`=?, `Direccion`=?,`Responsable_cap`=?  WHERE `id_titular`=?";


    // Prepara la sentencia SQL
    $stmt = $mysqli->prepare($query);

    // Vincular los parámetros a los marcadores de posición
    $stmt->bind_param("sssssssi", $Nombre_titular_editar, $Apellido_Paterno_Editar, $Apellido_Materno_Editar, $Telefono_titular_editar, $telefono_casa_edita, $Direccion, $responsable_cap, $id_titular_editar);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la edición fue exitosa, devuelve 'exito' al cliente
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../../event_logs/logs/" . $logFileName;
        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Success: Data updated successfully";
        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log.");
        }
    } else {
        throw new Exception($stmt->error);
    }

    // Cerrar la sentencia
    $stmt->close();
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage(); // Devuelve un mensaje de error más informativo.

    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

// ...


// Cerrar la conexión a la base de datos
$mysqli->close();
?>