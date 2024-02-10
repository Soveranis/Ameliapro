<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Verifica si se ha proporcionado un término de búsqueda
if (isset($_GET['term'])) {
    $return_arr = array(); // Inicializa un arreglo para almacenar los resultados

    // Divide el término de búsqueda en palabras separadas
    $terms = explode(' ', $_GET['term']);
    $nombre = isset($terms[0]) ? $terms[0] : ''; // Obtiene el primer término
    $apellidos = isset($terms[1]) ? $terms[1] : ''; // Obtiene el segundo término

    // Construye una consulta SQL para buscar en la base de datos
    $sql = "SELECT * FROM mxpt_titulares WHERE Nombre_titular LIKE '%$nombre%' AND (Apellido_Paterno_Titular LIKE '%$apellidos%' OR Apellido_Materno_Titular LIKE '%$apellidos%') LIMIT 10";

    // Ejecuta la consulta SQL
    $result = $mysqli->query($sql);

    if ($result) {
        // Recorre los resultados y los agrega al arreglo de resultados
        while ($row = $result->fetch_assoc()) {
            $row_array['value'] = $row['Nombre_titular'] . " " . $row['Apellido_Paterno_Titular'] . " " . $row['Apellido_Materno_Titular'];
            $row_array['id_titular'] = $row['id_titular'];
            $row_array['Nombre_titular'] = $row['Nombre_titular'] . ' ' . $row['Apellido_Paterno_Titular'] . ' ' . $row['Apellido_Materno_Titular'];
            $row_array['Direccion'] = $row['Direccion'];
            $row_array['Telefono_titular'] = $row['Telefono_titular'];
            $row_array['Nr'] = $row['id_titular'];

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
