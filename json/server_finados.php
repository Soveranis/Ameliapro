<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Inicia la sesión
session_start();

// Obtiene el nombre de usuario de la sesión
$username = $_SESSION['user_login_panteones'];

// Consulta para obtener datos del usuario
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);

// Obtiene los datos del usuario
$dat = mysqli_fetch_assoc($resul);

// Define la tabla y claves primarias
$table = 'mxpt_finados';
$primaryKey = 'id_finado';

// Define las columnas de la tabla
$columns = array(
    array('db' => 'id_finado', 'dt' => 0),
    array('db' => 'Nombre_finado', 'dt' => 1),
    array('db' => 'Apellido_Paterno_Finado', 'dt' => 2),
    array('db' => 'Apellido_Materno_Finado', 'dt' => 3),
    array('db' => 'Nombre_titular', 'dt' => 4),
    array('db' => 'Apellido_Paterno_Titular', 'dt' => 5),
    array('db' => 'Apellido_Materno_Titular', 'dt' => 6),
    array(
        'db' => 'usuario',
        'dt' => 7,
        'formatter' => function ($d, $row) {
            return '<div class="User_Resp">' . $d . '</div>';
        }
    ),
    array(
        'db' => 'Fecha_inhumacion',
        'dt' => 8,
        'formatter' => function ($d, $row) {
            return '<div class="date"> <input type="date" class="form-control date-input-view-only" value="' . date("Y-m-d", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }
    ),
    array(
        'db' => 'Fecha_Captura_Finado',
        'dt' => 9,
        'formatter' => function ($d, $row) {
            return '<div class="date"> <input type="date" class="form-control date-input-view-only" value="' . date("Y-m-d", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }
    ),
    array(
        'db' => 'id_finado',
        'dt' => 10,
        'formatter' => function ($d, $row) {
            $nombre = $row['Nombre_titular'] . ' ' . $row['Apellido_Paterno_Titular'] . ' ' . $row['Apellido_Materno_Titular'];
            $fechaInhumacion = $row['Fecha_inhumacion'];
            $nombreFinado = $row['Nombre_finado'];
            $apellidoPaternoFinado = $row['Apellido_Paterno_Finado'];
            $apellidoMaternoFinado = $row['Apellido_Materno_Finado'];
            $id_finado_2 = $row['id_finado'];
            $id_titular = $row['Titular'];

            return '<div class="btn recuperar_datos" data-toggle="modal" data-target="#opciones" 
            data-axel="' . $d . '" data-nombre="' . $nombre . '" data-fecha="' . $fechaInhumacion . '" 
            data-nombre-finado="' . $nombreFinado . '" data-apellido-paterno-finado="' . $apellidoPaternoFinado . '" 
            data-apellido-materno-finado="' . $apellidoMaternoFinado . '" data-id_finado_2="' . $id_finado_2 . '" 
            data-titular="' . $id_titular . '">
            <img src="../img/mas opciones.png" alt="user" style="width: 27px; object-fit: cover;">
        </div>';
        }
    ),
    array('db' => 'Titular', 'dt' => 11),
);
// Conexion usando los valores de la conexion mysql
$sql_details = array(
    'user' => $mysqli->real_escape_string(DB_USERNAME),
    'pass' => $mysqli->real_escape_string(DB_PASSWORD),
    'db'   => $mysqli->real_escape_string(DB_NAME),
    'host' => $mysqli->real_escape_string(DB_SERVER)
);

// Requiere el archivo necesario
require('Server.cide/finados.php');

// Devuelve los resultados en formato JSON
echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
);
?>
