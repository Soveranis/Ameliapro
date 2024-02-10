<?php
include("../../conexiones/conexion.php");

// archivo.php
$loca = $_POST['idFinado'];


$sqsl = $mysqli->query("SELECT *
                        FROM catalogo_historico_titulares AS ser
                        INNER JOIN mxpt_usuarios AS users ON ser.Responsable_Cambio = users.id_usuario
                        INNER JOIN mxpt_titulares AS titu ON ser.id_titular = titu.id_titular
                        INNER JOIN mxpt_finados AS fina ON ser.id_finado = fina.id_finado
                        WHERE ser.id_finado='$loca'");

if ($sqsl === false) {
    printf("Error en la consulta: %s\n", mysqli_error($mysqli)); // Imprimir el error si la consulta falla
    exit();
}

while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['Nombre_titular'] . "<br>" . $row['Apellidos_titular'] . "</td>";
    echo "<td>" . $row['Nombre_finado'] . "<br>" . $row['Apellidos_finado'] . "</td>";
    echo "<td><div class='User_Resp'>" . $row['Usuario'] . "</div></td>";
    echo "<td>   <div class='date'> <input type='datetime-local' class='form-control date-input-view-only' value='" . $row['Fecha_Cambio'] . "' readonly ><i class='fa-regular fa-calendar caler'></i> </div></td></td>";
 
    echo "</tr>";
}
?>