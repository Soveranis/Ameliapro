<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Lee las variables de la solicitud
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'];
$columnIndex = $_POST['order'][0]['column'];
$columnName = $_POST['columns'][$columnIndex]['data'];
$columnSortOrder = $_POST['order'][0]['dir'];
$searchValue = mysqli_real_escape_string($mysqli, $_POST['search']['value']);

// Variables de búsqueda por fecha
$buscarFechaInicio = mysqli_real_escape_string($mysqli, $_POST['buscarFechaInicio']);
$buscarFechaFin = mysqli_real_escape_string($mysqli, $_POST['buscarFechaFin']);

// Consulta de búsqueda normal
$searchQuery = " ";
if ($searchValue != '') {
    $searchQuery = " and (Folio_Panteones like '%" . $searchValue . "%' or Folio_Tesoreria like '%" . $searchValue . "%') ";
}

// Filtrar por fecha si se proporcionan fechas de inicio y fin
if ($buscarFechaInicio != '' && $buscarFechaFin != '') {
    $searchQuery .= " and (Fecha_acreditacion between '" . $buscarFechaInicio . "' and '" . $buscarFechaFin . "' ) ";
}

// Número total de registros sin filtrar
$sel = mysqli_query($mysqli, "SELECT count(*) as allcount FROM mxpt_pagos AS fina
        INNER JOIN mxpt_tumbas AS tit ON tit.id_tumba = fina.id_tumba INNER JOIN mxpt_finados AS sdd ON sdd.id_finado = tit.Finado_tum INNER JOIN mxpt_titulares AS lts ON lts.id_titular = sdd.Titular INNER JOIN mxpt_servicios AS ser ON ser.id_servicio = fina.id_servicio INNER JOIN mxpt_usuarios AS users ON fina.Responsable_cap = users.id_usuario");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

// Número total de registros con filtrado
$sel = mysqli_query($mysqli, "SELECT count(*) as allcount FROM mxpt_pagos AS fina
       INNER JOIN mxpt_tumbas AS tit ON tit.id_tumba = fina.id_tumba INNER JOIN mxpt_finados AS sdd ON sdd.id_finado = tit.Finado_tum INNER JOIN mxpt_titulares AS lts ON lts.id_titular = sdd.Titular INNER JOIN mxpt_servicios AS ser ON ser.id_servicio = fina.id_servicio INNER JOIN mxpt_usuarios AS users ON fina.Responsable_cap = users.id_usuario WHERE 1 " . $searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

// Obtiene los registros
$empQuery = "SELECT * FROM mxpt_pagos AS fina
       INNER JOIN mxpt_tumbas AS tit ON tit.id_tumba = fina.id_tumba INNER JOIN mxpt_finados AS sdd ON sdd.id_finado = tit.Finado_tum INNER JOIN mxpt_titulares AS lts ON lts.id_titular = sdd.Titular INNER JOIN mxpt_servicios AS ser ON ser.id_servicio = fina.id_servicio INNER JOIN mxpt_usuarios AS users ON fina.Responsable_cap = users.id_usuario WHERE 1 " . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
$empRecords = mysqli_query($mysqli, $empQuery);
$data = array();

// Almacena los registros en un arreglo
while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "Folio_Panteones" => $row['Folio_Panteones'],
        "Nombre_Servicio" => $row['Nombre_Servicio'],
        "Folio_Tesoreria" => $row['Folio_Tesoreria'],
        "Fecha_acreditacion" => $row['Fecha_acreditacion'],
        "Nombre_titular" => $row['Nombre_titular'],
        "Monto_Pago" => $row['Monto_Pago'],
    );
}

// Respuesta
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
die;
