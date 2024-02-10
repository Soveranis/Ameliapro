<?php
include("../../conexiones/conexion.php");
$query = "SELECT MAX(id_pago) AS max_id FROM mxpt_pagos";
$result = mysqli_query($mysqli, $query);
$row = mysqli_fetch_assoc($result);
$folio_number = $row['max_id'] + 1;
$folio = "PT-" . $folio_number;
echo $folio;
?>
