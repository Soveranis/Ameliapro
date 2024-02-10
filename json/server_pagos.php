<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Inicia la sesión
session_start();

// Obtiene el nombre de usuario de la sesión
$username = $_SESSION['user_login_panteones'];

// Consulta la base de datos para obtener información del usuario actual
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);

// Define los detalles de la tabla y las columnas
$table = 'mxpt_pagos';
$primaryKey = 'id_pago';
$columns = array(
    array('db' => 'id_pago',  'dt' => 0),
    array('db' => 'Nr_tumba',  'dt' => 1),
    array(
        'db' => 'Folio_Tesoreria',
        'dt' => 2, // Asegúrate de que este índice sea el siguiente disponible
        'formatter' => function ($d, $row) {
            $class = '';
            $text = '';
            if ($d === "") { // Si el folio está vacío
                $class = 'alert alert-danger'; // Clase de alerta para "Sin Folio"
                $text = 'Sin Folio';
            } else { // Si hay un folio
                $class = ''; // Puedes agregar una clase si es necesario
                $text = $d; // Muestra el folio
            }
            return '<div class="' . $class . '">' . $text . '</div>';
        }
    ),

    array('db' => 'Folio_Panteones',   'dt' => 3),
    array('db' => 'Nombre_titular', 'dt' => 4),
    array('db' => 'Monto_Pago',  'dt' => 5),
    array('db' => 'Nombre_Servicio',  'dt' => 6),
    array(
        'db' => 'Estatus',
        'dt' => 7, // Asegúrate de que este índice sea el siguiente disponible
        'formatter' => function ($d, $row) {
            $class = '';
            $text = '';
            // Creamos una validacion para pintar de colores el estatus
            if ($d === "Corriente") {
                $class = 'alert alert-verde';
                $text = 'Corriente';
            } elseif ($d === "Pendiente") {
                $class = 'alert alert-warning';
                $text = 'Pendiente';
            } else {
                $class = 'alert alert-danger';
                $text = 'Retrasado';
            }
            return '<div class="' . $class . '">' . $text . '</div>';
        }
    ),
    array(
        'db' => 'Fecha_acreditacion',
        'dt' => 8,
        'formatter' => function ($d, $row) {
            if (empty($d)) {
                return '<div class="date"> <input type="datetime-local" class="form-control date-input-view-only"readonly ><i class="fa-regular fa-calendar caler"></i>  </div>';
            }
            return '<div class="date"> <input type="datetime-local" class="form-control date-input-view-only" value="' . date("Y-m-d\TH:i:s", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }
    ),
    array(
        'db' => 'Fecha_Captura_Pago',
        'dt' => 9,
        'formatter' => function ($d, $row) {
            if (empty($d)) {
                return '<div class="date">Fecha no disponible</div>';
            }
            return '<div class="date"> <input type="datetime-local" class="form-control date-input-view-only" value="' . date("Y-m-d\TH:i:s", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }

    ),
    array(
        'db' => 'usuario',
        'dt' => 10,
        'formatter' => function ($d, $row) {
            return '<div class="User_Resp">' . $d . '</div>';
        }
    ),
    array(
        'db' => 'id_pago',
        'dt' => 11,
        'formatter' => function ($d, $row) {
            $id_pago = $row['id_pago'];
            $folio_tesoreria = $row['Folio_Tesoreria']; // Asegúrate de tener acceso a esta columna en tu consulta

            // Verifica si el folio está vacío o no
            if (empty($folio_tesoreria) || $folio_tesoreria === " ") {
                return '<div class="btn recuperar_datos_edit" data-toggle="modal" data-target="#Editar_Orden" data-identificador_orden="' . $id_pago . '">
                  Editar
                        </div>';
            } else {
                return '<div class="btn disabled">
                  Folio existente
                        </div>';
            }
        }
    ),

    array(
        'db' => 'Fecha_Limite_Pago',
        'dt' => 12,
        'formatter' => function ($d, $row) {
            if ($d == '0000-00-00' || empty($d)) {
                return '<div class="date"> <input type="date" class="form-control date-input-view-only" readonly><i class="fa-regular fa-calendar caler"></i></div>';
            }
            return '<div class="date"> <input type="date" class="form-control date-input-view-only" value="' . date("Y-m-d", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }

    ),
    array(
        'db' => 'id_pago',
        'dt' => 13,
        'formatter' => function ($d, $row) {
            $id_pago = $row['id_pago'];
            $Folio_Panteones = $row['Folio_Panteones'];
            $Folio_Tesoreria = $row['Folio_Tesoreria'];
            return '<div class="btn historial" data-toggle="modal" data-target="#myModal_editar_titular" data-identificador_orden="' . $id_pago . '" data-folio_panteones="' . $Folio_Panteones . '" data-folio_tesoreria="' . $Folio_Tesoreria . '">
                        Consultar
                    </div>';
        }
    ),

);

// Conexion usando los valores de la conexion mysql
$sql_details = array(
    'user' => $mysqli->real_escape_string(DB_USERNAME),
    'pass' => $mysqli->real_escape_string(DB_PASSWORD),
    'db'   => $mysqli->real_escape_string(DB_NAME),
    'host' => $mysqli->real_escape_string(DB_SERVER)
);

// Requiere el archivo de servidor y devuelve los resultados en formato JSON
require('Server.cide/Pagos.php');
echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
);
?>