<?php
include("../conexiones/conexion.php");
if (isset($_GET['term'])) {
    $return_arr = array();

    $terms = explode(' ', $_GET['term']);
    
    // Construye la consulta SQL basada en los términos ingresados
    $sql = "SELECT * FROM catalogo_codigos_postales WHERE 1=1";
    foreach ($terms as $term) {
        $sql .= " AND (Estado LIKE '%$term%' OR Municipio LIKE '%$term%' OR Colonia LIKE '%$term%' OR Codigo_postal LIKE '%$term%')";
    }
    $sql .= " LIMIT 10";

    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row_array['value'] = $row['Estado'].' '.$row['Municipio'] . ' ' . $row['Colonia'] . ' ' . $row['Codigo_postal'];
            $row_array['Direccion_completa'] = $row['Estado'].', '.$row['Municipio'] . ', ' . $row['Colonia'] . ', ' . $row['Codigo_postal'];

            array_push($return_arr, $row_array);
        }
        $result->free();
    } else {
   // Si no se encuentran resultados, agrega el mensaje "Dirección inexistente"
$row_array['value'] = "La dirección no se encuentra.";

        array_push($return_arr, $row_array);
    }
    
    echo json_encode($return_arr);
}

?>