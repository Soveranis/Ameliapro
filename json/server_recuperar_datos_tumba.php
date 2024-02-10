<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Verifica si se ha proporcionado un término de búsqueda
if (isset($_GET['term'])) {
    $return_arr = array(); // Inicializa un arreglo para almacenar los resultados

    // Divide el término de búsqueda en palabras separadas
    $terms = explode(' ', $_GET['term']);
    $nombre = isset($terms[0]) ? $terms[0] : ''; // Obtiene el primer término

    // Construye una consulta SQL para buscar en la base de datos
    $sql = "SELECT * FROM mxpt_tumbas AS tum
    INNER JOIN mxpt_finados AS fina ON tum.Finado_tum  = fina.id_finado
    INNER JOIN mxpt_titulares AS tit ON tit.id_titular = fina.Titular
    INNER JOIN mxpt_panteones AS pante ON tum.Panteon_Tum  = pante.id_panteon
    INNER JOIN catalogo_tipo_tumba AS tipo_tum ON tum.Tipo_Tumba_tum  = tipo_tum.id_tipo_tumba
    INNER JOIN mxpt_usuarios AS user ON tum.Responsable_cap_tum  = user.id_usuario   
    WHERE tum.Nr_tumba LIKE '%$nombre%'";

    // Ejecuta la consulta SQL
    $result = $mysqli->query($sql);

    if ($result) {
        // Recorre los resultados y los agrega al arreglo de resultados
        while ($row = $result->fetch_assoc()) {
            $row_array['value'] = $row['Nr_tumba'] . ' ' . $row['Nombre_titular'] . ' ' . $row['Apellido_Paterno_Titular'];
            $row_array['Nombre_finado'] = $row['Nombre_finado'] . ' ' . $row['Apellido_Paterno_Finado'];
            $row_array['Nombre_titular'] = $row['Nombre_titular'] . ' ' . $row['Apellido_Paterno_Titular'];
            $row_array['Telefono_titular'] = $row['Telefono_titular'];
            $row_array['id_tumba'] = $row['id_tumba'];
            $row_array['Tipo_Tumba'] = $row['Tipo_Tumba_tum'];
            $row_array['Medidas'] = $row['Medidas_tum'];
            $row_array['Lote'] = $row['Lote_tum'];
            $row_array['Fecha_apertura'] = $row['Fecha_apertura_tum'];

            // Agrega el resultado actual al arreglo de resultados
            array_push($return_arr, $row_array);
        }

        // Libera la memoria de los resultados
        $result->free();
    }

    // Devuelve los resultados en formato JSON
    echo json_encode($return_arr);
}
?>
