<?php
$APIKEY = "ab5874f7c335aba5a2313b061e64b8e3f8855802";
$estadoId = $_GET['estado_id'];
$url = "https://api.tau.com.mx/dipomex/v1/municipios?id_estado=" . $estadoId;

$headers = [
    "APIKEY: $APIKEY"
];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorMsg = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($httpCode != 200 || $errorMsg) {
    $logType = "API Error";
    $errorDescription = $errorMsg ? $errorMsg : "HTTP Code: " . $httpCode;

    $postData = http_build_query([
        'logType' => $logType,
        'error' => $errorDescription,
        'username' => $_SESSION['user_login_panteones']
    ]);

    $ch = curl_init('http://localhost/Panteones/User_Pante/api/generar_log.php'); // Asegúrate de que esta URL sea correcta
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["error" => "Error al comunicarse con la API."]);
} else {
    echo $response;
}
?>