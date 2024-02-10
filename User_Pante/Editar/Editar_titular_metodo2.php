<?php
include("../../conexiones/conexion.php");

session_start();
$username = $_SESSION['user_login_panteones'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

// Obtén los datos del formulario
$Nombre_titular = $_POST['Nombre_titular'];
$Apellido_Paterno_m2_editar = $_POST['Apellido_Paterno_m2_editar'];
$Apellido_Materno_m2_editar = $_POST['Apellido_Materno_m2_editar'];
$Telefono_titular = $_POST['telefono_editar_m2_editar'];
$telefono_casa = $_POST['telefono_casa'];
$id_titular = $_POST['id_titular'];

// Determina el valor de la dirección según la lógica del formulario
if (isset($_POST['Direccion'])) {
    $Direccion = $_POST['Direccion'];

} else if (isset($_POST['checkDireccion_2'])) {
    $Direccion = $_POST['Direccion_completa_metodo_2'];

} else if (isset($_POST['agregar_manualmente_m2'])) {
    $Direccion = $_POST['agregar_manualmente_m2'];

} else {
    // Manejar el caso en el que ninguna de las variables esté presente
    // Puedes asignar un valor predeterminado o mostrar un mensaje de error
    $Direccion = "Valor predeterminado o mensaje de error";
}


try {
    // Prepara la consulta SQL con marcadores de posición
    $query = "UPDATE `mxpt_titulares` SET `Nombre_titular`=?,`Apellido_Paterno_Titular`=?,`Apellido_Materno_Titular`=?,`Telefono_titular`=?,`Telefono_Casa`=?, `Direccion`=?,`Responsable_cap`=?  WHERE `id_titular`=?";

    // Prepara la sentencia
    $stmt = $mysqli->prepare($query);

    // Asocia los valores a los marcadores de posición
    $stmt->bind_param("sssssssi", $Nombre_titular, $Apellido_Paterno_m2_editar, $Apellido_Materno_m2_editar, $Telefono_titular, $telefono_casa, $Direccion, $responsable_cap, $id_titular);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        echo 'exito'; // Si la inserción fue exitosa, devuelve 'exito' al cliente

        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    
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
    $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;

    // Incluye solo el nombre del archivo y el número de línea en el mensaje de log
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$stmt->close(); // Cierra la sentencia
$mysqli->close(); // Cierra la conexión a la base de datos
?>
