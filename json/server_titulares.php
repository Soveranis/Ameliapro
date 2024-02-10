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
$table = 'mxpt_titulares';
$primaryKey = 'id_titular';
$columns = array(
    array( 'db' => 'id_titular', 'dt' => 0 ),
    array( 'db' => 'Nombre_titular', 'dt' => 1 ),
    array( 'db' => 'Apellido_Paterno_Titular', 'dt' => 2 ),
    array( 'db' => 'Apellido_Materno_Titular', 'dt' => 3 ),
    array( 'db' => 'Telefono_titular', 'dt' => 4 ),
    array( 'db' => 'Telefono_Casa', 'dt' => 5 ),
    array(
        'db' => 'Direccion',
        'dt' => 6,
        'formatter' => function($d, $row) {
            $direccionUrl = urlencode($d);
            return '<div class="mapa"> <a href="https://www.google.com.mx/maps/search/' . $direccionUrl . '" target="_blank">' . $d . '</a> </div>';
        }
    ),
    array(
        'db' => 'usuario',
        'dt' => 7,
        'formatter' => function($d, $row) {
            return '<div class="User_Resp">'.$d.'</div>';
        }
    ),
    array( 
        'db' => 'Fecha_Captura_Titular',
        'dt' => 8,
        'formatter' => function($d, $row) {
            return '<div class="date"> <input type="date" class="form-control date-input-view-only" value="' . date("Y-m-d", strtotime($d)) . '" readonly ><i class="fa-regular fa-calendar caler"></i> </div>';
        }
    ),
    array(
        'db' => 'id_titular',
        'dt' => 9,
        'formatter' => function($d, $row) use ($dat) {
            // Valores originales
            $nombre_metodo_1 = $row['Nombre_titular'];
            $apellido_paterno_metodo_1 = $row['Apellido_Paterno_Titular'];
            $apellido_materno_metodo_1 = $row['Apellido_Materno_Titular'];
            $Telefono_Casa_metodo_1 = $row['Telefono_Casa'];
            $telefono_metodo_1 = $row['Telefono_titular'];
            $id_titular_metodo_1 = $row['id_titular'];
            $direccion_metodo_1 = $row['Direccion'];

            // Valores con sufijo _metodo2 (son los mismos valores en este caso)
            $nombre_metodo2 = $row['Nombre_titular'];
            $apellido_paterno_metodo2 = $row['Apellido_Paterno_Titular'];
            $apellido_materno_metodo2 = $row['Apellido_Materno_Titular'];
            $telefono_metodo2 = $row['Telefono_titular'];
            $Telefono_Casa_metodo_2 = $row['Telefono_Casa'];
            $id_titular_metodo2 = $row['id_titular'];
            $direccion_metodo2 = $row['Direccion'];

            // Devolver el botón con ambos conjuntos de atributos data-*
            return '<div class="btn recuperar_datos" data-toggle="modal" data-target="#opciones" 
            data-nombre="' . $nombre_metodo_1 . '" data-apellido_paterno="' . $apellido_paterno_metodo_1 . '" data-apellido_materno="' . $apellido_materno_metodo_1 . '" data-telefono="' . $telefono_metodo_1 . '" data-telefono_casa="' . $Telefono_Casa_metodo_1 . '" data-direccion="' . $direccion_metodo_1 . '" data-id="' . $id_titular_metodo_1 . '"
            data-nombre_metodo2="' . $nombre_metodo2 . '" data-apellido_paterno_metodo2="' . $apellido_paterno_metodo2 . '" data-apellido_materno_metodo2="' . $apellido_materno_metodo2 . '" data-telefono_metodo2="' . $telefono_metodo2 . '" data-telefono_casa_metodo2="' . $Telefono_Casa_metodo_2 . '" data-direccion_metodo2="' . $direccion_metodo2 . '" data-id_metodo2="' . $id_titular_metodo2 . '">
            <img src="../img/mas opciones.png" alt="user" style="width: 27px; object-fit: cover;">
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

// Requiere el archivo de servidor DataTables
require( 'Server.cide/titulares.php' ); 

// Devuelve los datos en formato JSON utilizando Simple Server Processing
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
