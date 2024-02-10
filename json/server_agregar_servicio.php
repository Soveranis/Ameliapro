<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Verifica si se recibió el parámetro 'term' en la solicitud GET
if (isset($_GET['term'])) {
    // Inicializa un arreglo para almacenar los resultados
    $return_arr = array();

    // Divide el término de búsqueda en palabras
    $terms = explode(' ', $_GET['term']);
    // Obtiene el primer término (nombre) o un valor vacío si no está presente
    $nombre = isset($terms[0]) ? $terms[0] : '';

    // Construye la consulta SQL para buscar servicios que coincidan con el nombre
    $sql = "SELECT * FROM mxpt_servicios WHERE Nombre_Servicio LIKE '%$nombre%'";

    // Ejecuta la consulta
    $result = $mysqli->query($sql);

    // Verifica si la consulta fue exitosa
    if ($result) {
        // Recorre los resultados de la consulta
        while ($row = $result->fetch_assoc()) {
            // Crea un arreglo asociativo con los datos del servicio
            $row_array['value'] = $row['Nombre_Servicio'];
            $row_array['id_servicio'] = $row['id_servicio'];
            $row_array['Nombre_Servicio'] = $row['Nombre_Servicio'];
            $row_array['Monto'] = $row['Monto'];
            $row_array['Monto_Orden'] = $row['Monto'];
            $row_array['Descripcion'] = $row['Descripcion'];

            // Agrega el arreglo al arreglo de resultados
            array_push($return_arr, $row_array);
        }

        // Libera los resultados de la consulta
        $result->free();
    }

    // Devuelve los resultados en formato JSON
    echo json_encode($return_arr);
}
