<?php
include("../../conexiones/conexion.php");

session_start();
$username = isset($_POST['username']) ? $_POST['username'] : null;

    $Inser = "SELECT * FROM `mxpt_usuarios` WHERE `usuario`='$username'";
    $resul = mysqli_query($mysqli, $Inser);
    $dat = mysqli_fetch_assoc($resul);
    $id_usuario = $dat['id_usuario'];
    echo $id_usuario;
    if (isset($_POST['error'])) {
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "C:\\xampp\\htdocs\\Panteones\\event_logs\\logs\\" . $logFileName;
    
        $logType = filter_input(INPUT_POST, 'logType', FILTER_SANITIZE_STRING);
        $errorMsg = filter_input(INPUT_POST, 'error', FILTER_SANITIZE_STRING);
    
        if ($logType && $errorMsg) {
            // Asegúrate de que $id_usuario tiene un valor antes de usarlo
            $userIdLog = isset($id_usuario) ? $id_usuario : "Desconocido";
            
            // Usa comillas dobles para la cadena del mensaje del log
            $logMessage = date("Y-m-d H:i:s") . " - User-id: $userIdLog - Log type: $logType - $errorMsg";
            
           
            if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
                error_log("Error al escribir en el archivo de log.");
            }
        }
    }
