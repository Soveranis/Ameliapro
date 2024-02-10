<?php
include("../conexiones/conexion.php");

if (isset($_GET['term'])) {
    $return_arr = array();

    $terms = explode(' ', $_GET['term']);
    $nombre = isset($terms[0]) ? $terms[0] : '';
    $apellido = isset($terms[1]) ? $terms[1] : '';

    // Usando consultas preparadas para prevenir inyección SQL
    $sql = "SELECT * FROM mxpt_finados AS fina
    INNER JOIN mxpt_titulares AS tit ON tit.id_titular = fina.Titular
    INNER JOIN mxpt_usuarios AS users ON fina.Responsable_cap = users.id_usuario 
    WHERE tit.Nombre_titular LIKE ? OR tit.Apellido_Paterno_Titular LIKE ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $nombre = '%' . $nombre . '%';
        $apellido = '%' . $apellido . '%';
        $stmt->bind_param('ss', $nombre, $apellido);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row_array['value'] = "Titular: " . $row['Nombre_titular'] . ' ' . $row['Apellido_Paterno_Titular'] . ' ' . $row['Apellido_Materno_Titular'] . "\nFinado: " . $row['Nombre_finado']. ' ' . $row['Apellido_Paterno_Finado']. ' ' . $row['Apellido_Materno_Finado'];
            $row_array['id_finado'] = $row['id_finado'];
        
            array_push($return_arr, $row_array);
        }
        $stmt->close();
    } else {
        echo "Error: " . $mysqli->error;
    }

    echo json_encode($return_arr);
}