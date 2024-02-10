<?php
// Incluye el archivo de conexión a la base de datos
require '../conexiones/conexion.php';

// Inicializa un array para almacenar la salida
$output = array();

// Consulta SQL que une varias tablas
$sql = "SELECT * FROM catalogo_lotes lote
        INNER JOIN mxpt_panteones pant ON lote.id_panteon = pant.id_panteon
        INNER JOIN mxpt_usuarios user ON lote.Responsable_cap = user.id_usuario";

// Obtiene el número total de filas en la consulta
$totalQuery = mysqli_query($mysqli, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

// Define las columnas de la tabla
$columns = array(
    0 => 'id_lote',
    1 => 'Panteon',
    2 => 'Lote',
    3 => 'Usuario',
    4 => 'Fecha_Captura_lote'
);

// Verifica si se realizó una búsqueda
if (isset($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " WHERE id_lote LIKE '%" . $search_value . "'";
    $sql .= " OR Panteon LIKE '%" . $search_value . "'";
    $sql .= " OR Lote LIKE '%" . $search_value . "'";
    $sql .= " OR Usuario LIKE '%" . $search_value . "'";
    $sql .= " OR Fecha_Captura_lote LIKE '%" . $search_value . "'";
}

// Verifica si se especificó un orden en las columnas
if (isset($_POST['order'])) {
    $column_name = $_POST['order'][0]['column'];
    $order = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY " . $columns[$column_name] . " " . $order;
} else {
    $sql .= " ORDER BY id_lote DESC";
}

// Verifica si se especificó un límite de resultados
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = isset($_POST["start"]) ? intval($_POST["start"]) : 4;
    $length = isset($_POST["length"]) ? intval($_POST["length"]) : 4;
    $sql .= " LIMIT $start,$length";
}

// Ejecuta la consulta SQL
$query = mysqli_query($mysqli, $sql);

// Obtiene el número de filas en el resultado
$count_rows = mysqli_num_rows($query);

// Inicializa un array para los datos de la tabla
$data = array();

// Recorre las filas de resultados
while ($row = mysqli_fetch_assoc($query)) {
    $sub_array = array();
    $sub_array[] = $row['id_lote'];
    $sub_array[] = $row['Panteon'];
    $sub_array[] = $row['Lote'];
    $sub_array[] = $row['Usuario'];
    $sub_array[] = $row['Fecha_Captura_lote'];
    $sub_array[] = '<button class="btn btn-danger editar-btn" data-toggle="modal" data-dismiss="modal" data-target="#Editar_lote_modal" data-idlote="' . $row['id_lote'] . '" data-nombre="' . $row['Lote'] . '" data-idpanteon="' . $row['Panteon'] . '" data-identicador="' . $row['id_panteon'] . '">Editar</button>';

    $data[] = $sub_array;
}

// Prepara la salida en formato JSON
$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $count_rows,
    'recordsFiltered' => $total_all_rows,
    'data' => $data,
);

// Devuelve los resultados en formato JSON
echo json_encode($output);
