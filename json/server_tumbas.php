<?php
// Incluye el archivo de conexión a la base de datos
include("../conexiones/conexion.php");

// Inicia la sesión (si no está ya iniciada)
session_start();

// Obtiene el nombre de usuario de la sesión
$username = $_SESSION['user_login_panteones'];

// Consulta la base de datos para obtener información del usuario
$Inser = "SELECT * FROM mxpt_usuarios WHERE usuario='$username'";
$resul = mysqli_query($mysqli, $Inser);
$dat = mysqli_fetch_assoc($resul);
?>

<?php
// Configuración para DataTables
$table = 'mxpt_tumbas';
$primaryKey = 'id_tumba';
$columns = array(
    array( 'db' => 'Nr_tumba', 'dt' => 0 ),
    array( 'db' => 'Nombre_titular', 'dt' => 1 ),
    array( 'db' => 'Apellido_Paterno_Titular', 'dt' => 2 ),
    array( 'db' => 'Apellido_Materno_Titular', 'dt' => 3 ),
    array( 'db' => 'Nombre_finado', 'dt' => 4 ),
    array( 'db' => 'Apellido_Paterno_Finado', 'dt' => 5 ),
    array( 'db' => 'Apellido_Materno_Finado', 'dt' => 6 ),
    array( 'db' => 'Panteon', 'dt' => 7 ),
    array( 'db' => 'Lote', 'dt' => 8 ),
    array(
        'db' => 'Estatus_tumba',
        'dt' => 9, // Asegúrate de que este índice sea el siguiente disponible
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
        'db' => 'usuario',
        'dt' => 11,
        'formatter' => function($d, $row) {
            return '<div class="User_Resp">'.$d.'</div>';
        }
    ),
    array(
        'db' => 'id_tumba',
        'dt' => 13,
        'formatter' => function($d, $row) {
            return '<div class="btn btn-danger recuperar_datos_editar_tumba">Editar</div>';
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

// Requiere el archivo de servidor DataTables
require( 'Server.cide/tumbas.php' ); 

// Devuelve los datos en formato JSON utilizando Simple Server Processing
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
