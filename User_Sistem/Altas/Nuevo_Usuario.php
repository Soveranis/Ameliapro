<?php
include("../../conexiones/conexion.php");
session_start();

$username = $_SESSION['user_login_sistemas'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

try {
    $nombre = $_POST['name'];
    $apellidos = $_POST['Apellido'];
    $tipo_usuario = $_POST['Tipo_Usuario'];
    $usuario = $_POST['Usuario'];
    $correo_2 = $_POST['correo'];
    $pass1 = $_POST['pass1'];

    $Estado = 'Activo';
    $fecha_captura = date('Y-m-d'); // Obtén la fecha actual

    // Verifica si el correo ya existe (pero no para el usuario actual)
    $consulta_correo = "SELECT * FROM mxpt_usuarios WHERE Mail = ? ";
    $stmt_verificar_correo = $mysqli->prepare($consulta_correo);

    if ($stmt_verificar_correo) { // Verificar si la preparación de la consulta fue exitosa
        $stmt_verificar_correo->bind_param("s", $correo_2);
        $stmt_verificar_correo->execute();
        $resultado_correo = $stmt_verificar_correo->get_result();

        if ($resultado_correo->num_rows > 0) {
            echo 'El_correo_existe'; // El correo ya existe
        } else {
            // Verifica si el usuario ya existe (pero no para el usuario actual)
            $consulta_usuario = "SELECT * FROM mxpt_usuarios WHERE Usuario = ? ";
            $stmt_verificar_usuario = $mysqli->prepare($consulta_usuario);

            if ($stmt_verificar_usuario) { // Verificar si la preparación de la consulta fue exitosa
                $stmt_verificar_usuario->bind_param("s", $usuario);
                $stmt_verificar_usuario->execute();
                $resultado_usuario = $stmt_verificar_usuario->get_result();

                if ($resultado_usuario->num_rows > 0) {
                    echo 'El_usuario_existe'; // El usuario ya existe
                } else {
                    $query = "INSERT INTO `mxpt_usuarios`(`Nombre`, `Apellidos`, `Tipo_Usuario`, `Estado`, `Usuario`, `Password`, `Mail`, `Fecha_Captura_Usuario`) VALUES (?,?,?,?,?,?,?,?);";

                    // Prepara la sentencia
                    $stmt = $mysqli->prepare($query);

                    // Asocia los valores a los marcadores de posición
                    $stmt->bind_param("ssssssss", $nombre, $apellidos, $tipo_usuario, $Estado, $usuario, $pass1, $correo_2, $fecha_captura);

                    if ($stmt->execute()) {
                        echo 'exito'; // La actualización fue exitosa
                        date_default_timezone_set('America/Mexico_City');
                        $logFileName = "log_" . date("Y-m-d") . ".txt";
                        $logFilePath = "../../event_logs/logs/" . $logFileName;
                        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Success: Data updated successfully";
                        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                            error_log("Error al escribir en el archivo de log.");
                        }
                    } else {
                        echo 'error';
                        // Manejo de errores
                        date_default_timezone_set('America/Mexico_City');
                        $logFileName = "log_" . date("Y-m-d") . ".txt";
                        $logFilePath = "../../event_logs/logs/" . $logFileName;
                        $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $stmt->error;
                        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                            error_log("Error al escribir en el archivo de log.");
                        }
                    }
                }
            } else {
                echo 'Error en la preparación de la consulta de usuario';
            }
        }
    } else {
        echo 'Error en la preparación de la consulta de correo';
    }
} catch (Exception $e) {
    echo 'error';
    // Manejo de errores
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../../event_logs/logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User-id: $responsable_cap - File: " . basename(__FILE__) . " - Line: " . __LINE__ . " - Error: " . $e->getMessage();
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
    }
}

$mysqli->close(); // Cierra la conexión a la base de datos
?>
