<?php
include("../../conexiones/conexion.php");

// archivo.php
$loca = $_POST['id_pago'];


$sqsl = $mysqli->query("SELECT *
                        FROM catalogo_historico_pagos AS ser
                        INNER JOIN mxpt_usuarios AS users ON ser.Responsable_Cambio = users.id_usuario
                        INNER JOIN mxpt_pagos AS pago ON ser.Orden_pago = pago.id_pago
                        WHERE ser.Orden_pago ='$loca'");

if ($sqsl === false) {
    printf("Error en la consulta: %s\n", mysqli_error($mysqli)); // Imprimir el error si la consulta falla
    exit();
}

while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) {
    echo "<tr>"; 
    echo "<td>" . $row['Orden_pago'] . "</td>";
    echo "<td>" . $row['Folio_Panteones'] . "</td>";
    echo "<td><div class='User_Resp'>" . $row['Usuario'] . "</div></td>";
    echo "<td>   <div class='date'> <input type='datetime-local' class='form-control date-input-view-only' value='" . $row['Fecha_Respaldo'] . "' readonly ><i class='fa-regular fa-calendar caler'></i> </div></td></td>";
    echo "</tr>";
}
?>