<?php
include("../conexiones/conexion.php");
session_start();
ini_set('display_errors', 1);

// Verifica si existe un contador de intentos en la sesión
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Verifica si se estableció un tiempo de bloqueo y si ha pasado
if (isset($_SESSION['block_time']) && time() < $_SESSION['block_time']) {
    echo 'Limite de intentos.';
} else {
    $nombreusuario = $_POST['c_usuario'];
    $contraseña = $_POST['c_password'];

    if ($_SESSION['login_attempts'] >= 3) {
        // Incrementa el contador de intentos fallidos
        $_SESSION['login_attempts']++;

        // Determina el tiempo de bloqueo según el número de intentos
        $tiempo_bloqueo = 10; // segundos por defecto

        if ($_SESSION['login_attempts'] == 3) {
            $tiempo_bloqueo = 10; // 10 segundos en el tercer intento
        }
        // Establece el tiempo de bloqueo
        $_SESSION['block_time'] = time() + $tiempo_bloqueo;

        // Reinicia el contador de intentos
        $_SESSION['login_attempts'] = 0;

        echo 'Limite de intentos. Espere ' . $tiempo_bloqueo . ' segundos antes de intentarlo nuevamente.';
        writeLog('Intento de inicio de sesión bloqueado temporalmente debido a demasiados intentos fallidos.', $nombreusuario);

        // Puedes añadir un return o exit aquí para evitar que el código continúe después de la condición de bloqueo.
        return;
    } else {
        if (empty($nombreusuario) && empty($contraseña)) {
            echo 'Ingrese Usuario y Contraseña';
            writeLog('Intento de inicio de sesión sin usuario y contraseña.', $nombreusuario);
        } elseif (empty($nombreusuario)) {
            echo 'Ingrese Usuario';
            writeLog('Intento de inicio de sesión sin usuario.', $nombreusuario);
        } elseif (empty($contraseña)) {
            echo 'Ingrese Contraseña';
            writeLog('Intento de inicio de sesión sin contraseña.', $nombreusuario);
        } else {
            $consulta = "SELECT Usuario,Estado,Password,Tipo_Usuario  FROM `mxpt_usuarios` WHERE `Usuario` = '" . mysqli_real_escape_string($mysqli, $nombreusuario) . "';";
            $resultado = mysqli_query($mysqli, $consulta);

            if (mysqli_num_rows($resultado) == 1) {
                $row = mysqli_fetch_assoc($resultado);

                if ($row['Estado'] === 'Activo') {
                    if ($row['Password'] == $contraseña) {
                        if ($row['Tipo_Usuario'] == 'Administrador Sistemas') {
                            $_SESSION['user_login_sistemas'] = $nombreusuario;
                            echo 'Administrador Sistemas';
                            $_SESSION['login_attempts'] = 0;
                            // Agregar log de inicio de sesión exitoso
                            writeLog('Inicio de sesión exitoso como Administrador Sistemas.', $nombreusuario);
                        } elseif ($row['Tipo_Usuario'] == 'Administrador Panteones') {
                            $_SESSION['user_login_panteones'] = $nombreusuario;
                            echo 'Administrador Panteones';
                            $_SESSION['login_attempts'] = 0;
                            // Agregar log de inicio de sesión exitoso
                            writeLog('Inicio de sesión exitoso como Administrador Panteones.', $nombreusuario);
                        } elseif ($row['Tipo_Usuario'] == 'Tesoreria') {
                            $_SESSION['user_login_tesoreria'] = $nombreusuario;
                            echo 'Tesoreria';
                            $_SESSION['login_attempts'] = 0;
                            // Agregar log de inicio de sesión exitoso
                            writeLog('Inicio de sesión exitoso como Tesoreria.', $nombreusuario);
                        } elseif ($row['Tipo_Usuario'] == 'Tecnosolucionext') {
                            $_SESSION['user_login_tecno'] = $nombreusuario;
                            echo 'Tecnosolucionext';
                            $_SESSION['login_attempts'] = 0;
                        }                          
                    } else {
                        echo 'Contraseña incorrecta';
                        // Incrementa el contador de intentos fallidos
                        $_SESSION['login_attempts']++;
                        writeLog('Intento de inicio de sesión con contraseña incorrecta.', $nombreusuario);
                    }
                } else {
                    echo 'Usuario Inactivo';
                    writeLog('Intento de inicio de sesión con usuario inactivo.', $nombreusuario);
                }
            } else {
                echo 'Usuario Inexistente';
                // Incrementa el contador de intentos fallidos
                $_SESSION['login_attempts']++;
                writeLog('Intento de inicio de sesión con usuario inexistente.', $nombreusuario);
            }
        }
    }
}









function writeLog($message, $user)
{
    date_default_timezone_set('America/Mexico_City');
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    $logFilePath = "../event_logs/Logs/" . $logFileName;
    $logMessage = date("Y-m-d H:i:s") . " - User: $user - Message: $message";

    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        error_log("Error al escribir en el archivo de log.");
        // Enviar el error a la consola
        error_log("Error al escribir en el archivo de log.", 0);
    }
}
