<?php
$APIKEY = "ab5874f7c335aba5a2313b061e64b8e3f8855802";
if (isset($_GET['id_estado']) && isset($_GET['id_mun'])) {
    $estadoId = $_GET['id_estado'];
    $municipioId = $_GET['id_mun'];
    $url = "https://api.tau.com.mx/dipomex/v1/colonias?id_estado=" . $estadoId . "&id_mun=" . $municipioId;

    $headers = [
        "APIKEY: $APIKEY"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => true, "message" => "id_estado or id_mun is missing"]);
}
?>