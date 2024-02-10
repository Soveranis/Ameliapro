<?php
include("../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_tesoreria'];
if (!isset($_SESSION['user_login_tesoreria']) || empty($_SESSION['user_login_tesoreria'])) {
    header('Location: ../');
    exit(); // Asegúrate de terminar la ejecución aquí
}

$query = "SELECT Nombre, Apellidos, Mail, Usuario, Tipo_Usuario, Password_Temporal FROM mxpt_usuarios WHERE Usuario = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellidos, $Mail, $Usuario, $Tipo_Usuario, $Password_Temporal);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error en la consulta: " . $mysqli->error);
}
if ($Password_Temporal == 1) {
    echo '<script>';
    echo 'window.addEventListener("DOMContentLoaded", function() {';
    echo '  $("#cambiar_contra").modal("show");'; // Muestra el modal automáticamente
    echo '});';
    echo '</script>';
} else {
    // Realiza acciones relacionadas con $Password_Temporal igual a 0
}



$query_2 = "SELECT Logo_Principal, Logo_Secundario, favicoin FROM catalogo_configuracion";
$stmt_2 = $mysqli->prepare($query_2);
$stmt_2->execute();
$stmt_2->bind_result($logoPrincipal_blob, $logoSecundario_blob, $favicoin_blob);
$stmt_2->fetch();

$logoPrincipal_data = base64_encode($logoPrincipal_blob);
$logoSecundario_data = base64_encode($logoSecundario_blob);
$favicoint_data = base64_encode($favicoin_blob);
$stmt_2->close();
?>
<!doctype html>
<html lang="en">

<head>
    <title>Departamento Panteones</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <?php
    if (isset($favicoin_blob) && !empty($favicoin_blob)) {
        $base64Image = base64_encode($favicoin_blob);
    } else {
        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
        $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
    } ?>
    <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" type="image/x-icon">
    <link href="../css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
</head>

<body>
    <div id="wrapper">

        <!-- Agregamos el menu lateral con los links respectivos -->
        <?php include('../Vistas/Menu_Lateral_user_tesoreria.php'); ?>


        <div class="main">
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Segundo contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="segundo-contenedor" class="ct-chart">
                                        <h3>Administrador de ordene Pago</h3>

                                        <!-- Date Filter -->
                                        <form id="formFecha">

                                            <table>
                                                <tr>
                                                    <!-- estos inputs se ocupan para las fuciones del excel cada uno cuanta con id el vamos a ocupar en javascript para realizar las funciones -->
                                                    <td>
                                                        <input type='date' id='buscar_inicio' class="form-control" placeholder='Fecha Inicio'>
                                                    </td>
                                                    <td class="boton_222">
                                                        <!--  buscar_inicio            buscar_fin       son los dos filtros de  fecha -->
                                                        <input type='date' id='buscar_fin' placeholder='Fecha fin' class="form-control" style="margin:10px;">
                                                    </td>
                                                    <td>
                                                        <input type='button' id="btn_search" value="Buscar" class="btn btn-success" style="margin:20px;">
                                                    </td>
                                                    <td>
                                                        <input type='button' id="btnLimpiar" value="Limpiar" class="btn btn-danger" style="margin:10px;">
                                                    </td>

                                                </tr>
                                            </table>
                                        </form>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id='Tabla_personal' class="table-bordered table-striped" style="width:100%">
                                                <caption class="caption table-bordered table-striped">Administrador
                                                    de
                                                    Finados</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Concepto tramite</th>
                                                        <th>Folio tesorería</th>
                                                        <th>Fecha Acreditacion</th>
                                                        <th>Nombre titular</th>
                                                        <th>Monto</th>

                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incluimos el footer -->
        <?php include('../Vistas/Footer.php'); ?>
    </div>


</body>

</html>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/validaciones_input.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="../json/script.js"></script>


<script>
    $(document).ready(function() {
        $(".datepicker").datepicker({
            "dateFormat": "yy-mm-dd",
            changeYear: true
        });
    });
    $(document).ready(function() {
        $('#btnLimpiar').click(function() {
            location.reload(); // Esto recargará la página
        });
    });
    // Este codigo funciona para poder ponerle el estilo de seleccionado a cada pagina en el menu lateral haciendolo automaticamente
    document.addEventListener("DOMContentLoaded", function() {
        const currentURL = window.location.href;

        const navLinks = document.querySelectorAll(".nav-link");
        navLinks.forEach(function(link) {
            if (currentURL.includes(link.getAttribute("href"))) {
                link.classList.add("selected");
            }
        });
    });
</script>