<?php
include("../conexiones/conexion.php");
session_start();

if (!isset($_SESSION['user_login_panteones']) || empty($_SESSION['user_login_panteones'])) {
    header('Location: ../index.php');
    exit(); // Asegúrate de terminar la ejecución aquí
}
$username = $_SESSION['user_login_panteones'];
$query = "SELECT Nombre, Apellidos, Mail, Usuario, Tipo_Usuario FROM mxpt_usuarios WHERE Usuario = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellidos, $Mail, $Usuario, $Tipo_Usuario);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error en la consulta: " . $mysqli->error);
}
$query = "SELECT Logo_Principal, Logo_Secundario , favicoin FROM catalogo_configuracion";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($logoPrincipal_blob, $logoSecundario_blob, $favicoin_blob);
$stmt->fetch();

$logoPrincipal_data = base64_encode($logoPrincipal_blob);
$logoSecundario_data = base64_encode($logoSecundario_blob);
$favicoint_data = base64_encode($favicoin_blob);
$stmt->fetch();

$query_4 = "SELECT Titulares, Finados, Tumbas, Reporte_General, Servicios, Panteones, Pagos FROM catalogo_modulos";
$stmt_5 = $mysqli->prepare($query_4);
$stmt_5->execute();

// Asociar variables a los resultados de la consulta
$stmt_5->bind_result($Titulares, $Finados, $Tumbas, $Reporte_General, $Servicios, $Panteones, $Pagos);

// Obtener los resultados
$stmt_5->fetch();
?>
<!doctype html>
<html lang="en">

<head>

    <head>
        <title>Departamento Panteones</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <?php
        if (isset($favicoin_blob) && !empty($favicoin_blob)) {
            $base64Image = base64_encode($favicoin_blob);
        } else {
            // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
            $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
        } ?>
        <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/main.css">
        <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    </head>

</head>
<style>
    .datepicker {
        margin-left: 11px;
        text-align: center;
        height: 35px;
        border-radius: 10px;
        width: 200px;
        border: solid 2px #E5E4E2;

    }
</style>

<body>
    <div id="wrapper">

        <!-- Agregamos el menu lateral con los links respectivos -->
        <?php include('../Vistas/Menu_Lateral_user_pante.php'); ?>




        <div class="main">
            <!-- MAIN CONTENT -->
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="demo-area-chart" class="ct-chart">
                                        <h3>Administrador De Reportes</h3><br><br>
                                        <hr>
                                        <!-- Date Filter -->
                                        <form id="formFecha">

                                            <input type='date' readonly id='buscar_inicio' class="datepicker" placeholder='Fecha Inicio' style="margin:10px;">
                                            <input type='date' readonly id='buscar_fin' class="datepicker" placeholder='Fecha fin' class="form-control" style="margin:10px;"> 
                                            <input type='button' id="btn_search" value="Buscar" class="btn btn-success">
                                            <input type='button' id="btnLimpiar" value="Limpiar" class="btn btn-danger ">

                                        </form>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id='Tabla_personal' class="display nowrap table-bordered table-striped">
                                                <caption class="caption table-bordered table-striped">Reporte</caption>
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
        <!-- Incluimos el Menu celular -->
        <?php include('../Vistas/Menu_celular.php'); ?>
        <!-- Incluimos el footer -->
        <?php include('../Vistas/Footer.php'); ?>
    </div>


</body>

</html>


<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>





<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
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