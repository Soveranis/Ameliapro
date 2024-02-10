<?php
include("../../conexiones/conexion.php");
session_start();

$username = $_SESSION['user_login_sistemas'];
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
$responsable_cap = $dat['id_usuario'];

try {
    // Obtén los datos del formulario
    $id_usuario = $_POST['id_usuario_editar'];
    $nombre = $_POST['name_2'];
    $apellidos = $_POST['Apellido_2'];
    $tipo_usuario = $_POST['Tipo_Usuario_2'];
    $estado = $_POST['estado_editar'];
    $usuario = $_POST['Usuario_2'];
    $correo_2 = $_POST['correo_2'];

    // Verifica si el correo ya existe (pero no para el usuario actual)
    $consulta_correo = "SELECT * FROM mxpt_usuarios WHERE Mail = ? AND id_usuario != ?";
    $stmt_verificar_correo = $mysqli->prepare($consulta_correo);
    $stmt_verificar_correo->bind_param("ss", $correo_2, $id_usuario);
    $stmt_verificar_correo->execute();
    $resultado_correo = $stmt_verificar_correo->get_result();

    if ($resultado_correo->num_rows > 0) {
        echo 'El_correo_existe'; // El correo ya existe
    } else {
        // Verifica si el usuario ya existe (pero no para el usuario actual)
        $consulta_usuario = "SELECT * FROM mxpt_usuarios WHERE Usuario = ? AND id_usuario != ?";
        $stmt_verificar_usuario = $mysqli->prepare($consulta_usuario);
        $stmt_verificar_usuario->bind_param("ss", $usuario, $id_usuario);
        $stmt_verificar_usuario->execute();
        $resultado_usuario = $stmt_verificar_usuario->get_result();

        if ($resultado_usuario->num_rows > 0) {
            echo 'El_usuario_existe'; // El usuario ya existe
        } else {
            // Ni el usuario ni el correo existen para otros usuarios, puedes continuar con la actualización
            $query = '';
            if ($estado === 'Activo') {
                // SQL para el estado "Activo"
                $query = "UPDATE mxpt_usuarios SET Nombre=?, Apellidos=?, Tipo_Usuario=?, Estado=?, Usuario=?, Mail=? WHERE id_usuario=?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ssssssi", $nombre, $apellidos, $tipo_usuario, $estado, $usuario, $correo_2, $id_usuario);
            } elseif ($estado === 'Inactivo') {
                // SQL para el estado "Inactivo"
                $query = "UPDATE mxpt_usuarios SET Nombre=?, Apellidos=?, Tipo_Usuario=?, Estado=?, Usuario=?, Mail=?, Fecha_Baja_Usuario=? WHERE id_usuario=?";
                $stmt = $mysqli->prepare($query);
                $fecha_actual = new DateTime("now", new DateTimeZone("America/Mexico_City"));
                $fecha_actual_str = $fecha_actual->format("Y-m-d H:i:s");
                $stmt->bind_param("sssssssi", $nombre, $apellidos, $tipo_usuario, $estado, $usuario, $correo_2, $fecha_actual_str, $id_usuario);
            }
            

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
                throw new Exception($stmt->error);
            }
        }
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