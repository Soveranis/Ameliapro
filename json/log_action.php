<?php
if (isset($_POST['action'])) {
    // Establece la zona horaria predeterminada para asegurarse de que las marcas de tiempo en el registro sean precisas
    date_default_timezone_set('America/Mexico_City');
    
    // Genera un nombre de archivo de registro basado en la fecha actual en el formato "log_AAAA-MM-DD.txt"
    $logFileName = "log_" . date("Y-m-d") . ".txt";
    
    // Define la ubicación y el nombre completo del archivo de registro
    $logFilePath = "../event_logs/logs/" . $logFileName;
    
    // Crea un mensaje de registro que incluye la marca de tiempo actual en formato "AAAA-MM-DD HH:MM:SS" y la acción registrada
    $logMessage = date("Y-m-d H:i:s") . " - Action: " . $_POST['action'];

    // Intenta escribir el mensaje de registro en el archivo de registro
    if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
        // Si se produce un error al escribir en el archivo, muestra un mensaje de error
        echo "Error al escribir en el archivo de log.";
    } else {
        // Si la acción se registra con éxito en el archivo de registro, muestra un mensaje indicando que la acción se ha registrado correctamente
        echo "Acción registrada con éxito.";
    }
}
?>
