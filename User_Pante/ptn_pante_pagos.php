<?php
include("../conexiones/conexion.php");
session_start();

if (!isset($_SESSION['user_login_panteones']) || empty($_SESSION['user_login_panteones'])) {
    header('Location: ../index.php');
    exit();
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

$query_modulos = "SELECT Titulares, Finados, Tumbas, Reporte_General, Servicios, Panteones, Pagos FROM catalogo_modulos";
$stmt_modulos = $mysqli->prepare($query_modulos);

if ($stmt_modulos) {
    $stmt_modulos->execute();
    $stmt_modulos->bind_result($Titulares, $Finados, $Tumbas, $Reporte_General, $Servicios, $Panteones, $Pagos);
    $stmt_modulos->fetch();
    $stmt_modulos->close();

    // Ahora puedes realizar operaciones con las variables obtenidas
    // Por ejemplo, imprimir o utilizar los valores de $Titulares, $Finados, $Tumbas, $Reporte_General, $Servicios, $Panteones
} else {
    die("Error en la consulta de módulos: " . $mysqli->error);
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
    <title>Pagos</title>
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
    <link href="../css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <!-- necesarios para el buscador -->
    <link rel="stylesheet" href="../css/Buscador.css">

    <style>
        #tbl-contact_filter {
            display: none;
        }
    </style>
</head>


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
                                        <h3>Órdenes de pago </h3><br><br>
                                        <hr>


                                        <button class="continue-application" data-toggle="modal" data-target="#Ordenpago">
                                            <div>
                                                <div class="pencil"></div>
                                                <div class="folder">
                                                    <div class="top">
                                                        <svg viewBox="0 0 24 27">
                                                            <path d="M1,0 L23,0 C23.5522847,-1.01453063e-16 24,0.44771525 24,1 L24,8.17157288 C24,8.70200585 23.7892863,9.21071368 23.4142136,9.58578644 L20.5857864,12.4142136 C20.2107137,12.7892863 20,13.2979941 20,13.8284271 L20,26 C20,26.5522847 19.5522847,27 19,27 L1,27 C0.44771525,27 6.76353751e-17,26.5522847 0,26 L0,1 C-6.76353751e-17,0.44771525 0.44771525,1.01453063e-16 1,0 Z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="paper"></div>
                                                </div>
                                            </div>
                                            Generar Orden Pago
                                        </button>


                                        <div class="table-responsive">
                                            <table class="table-bordered table-striped" name="tbl-contact" id="tbl-contact">
                                                <caption class="caption table-bordered table-striped">
                                                    Administrador de
                                                    Pagos</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.Orden</th>
                                                        <th>No.Tumba</th>
                                                        <th>Folio <br> Tesorería</th>
                                                        <th>Folio <br> Panteones</th>
                                                        <th>Titular</th>
                                                        <th>Monto</th>
                                                        <th>Servicio</th>
                                                        <th>Estatus</th>
                                                        <th>Fecha de Pago</th>
                                                        <th>Fecha de Captura</th>
                                                        <th>Responsable <br> Captura</th>
                                                        <th>Editar</th>
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

<!-- Modal -->
<div class="modal fade bs-example-modal-lg" id="Ordenpago" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                $query = "SELECT MAX(id_pago) AS max_id FROM mxpt_pagos";
                $result = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_assoc($result);
                $folio_number = $row['max_id'] + 1;
                $folio = "PT-" . $folio_number;
                ?>
                <h4 class="modal-title" id="folioLabel" style="float: right;">Folio: <?php echo $folio; ?></h4>

                <h4 class="modal-title" id="myModalLabel">Orden Pago</h4>
            </div>
            <div class="modal-body">
                <form id="nueva_orden">
                    <div class="Buscar_servicio">
                        <input type="hidden" id="ID_SERVICIO">
                        <input type="hidden" id="Nr_Folio" value="<?php echo $folio; ?>">
                        <input type="hidden" id="Monto_Orden">
                        <label for="">Buscar Servicio</label>
                        <div class="form">
                            <label for="search">
                                <input autocomplete="off" placeholder="Buscar Servicio" id="search" type="text">
                                <div class="icon_buscador">
                                    <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                    </svg>
                                </div>

                            </label>
                        </div>
                    </div>
                    <div id="alert-success" class="alert alert-success hidden custom-alert">
                        <strong>¡Correcto!</strong> Se guardado correctamente su orden de pago
                    </div>
                    <div id="alert-duplicate" class="alert alert-danger hidden custom-alert">
                        <strong>Error:</strong> No puede agregar dos servicios en la misma orden de pago. Elimine el
                        servicio que está en la tabla con el botón de <h4>Eliminar</h4>
                    </div>
                    <div id="alert-removed" class="alert alert-warning hidden custom-alert">
                        <strong>Ok</strong> Servicio quitado de la tabla
                    </div>
                    <div id="alert-agregar" class="alert alert-verde hidden custom-alert">
                        <strong>Ok</strong> Servicio Agregado a la tabla
                    </div>
                    <br>
                    <!-- ... otras alertas ... -->
                    <div class="table-responsive">
                        <table id="servicesTable" class="table-striped table-bordered " style="width:100%;">
                            <caption class="caption table-bordered table-striped">Servicio a Pagar</caption>
                            <thead>
                                <tr class="thead-tablas">
                                    <th>No.</th>
                                    <th>Servicio</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Mensaje por defecto cuando no hay registros -->
                                <tr id="noRecordsRow">
                                    <td colspan="5" class="no_register">No se encontraron registros</td>
                                </tr>
                                <!-- Los servicios seleccionados se agregarán aquí -->
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <input type="hidden" id="ID_TUMBA">
                    <div class="controls_flex">
                        <div class="Buscar_servicio">
                            <label for="">Buscar tumba</label>
                            <div class="form">
                                <label for="buscar_tumba">
                                    <input autocomplete="off" placeholder="Buscar tumba" id="buscar_tumba" type="text">
                                    <div class="icon_buscador">
                                        <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                        </svg>
                                    </div>

                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="">Nombre Finado</label>
                            <input type="text" class="form-control" required id="Nombre_finado" onkeydown="return false;">
                        </div>

                        <div class="form-group">
                            <label for="">Nombre Titular</label>
                            <input type="text" class="form-control" required id="Nombre_titular" onkeydown="return false;">
                        </div>
                        <div class="form-group">
                            <label for="">Teléfono</label>
                            <input type="text" class="form-control" required id="Telefono_titular" onkeydown="return false;">
                        </div>

                    </div>
                    <div class="controls_flex">

                        <div class="form-group">
                            <label for="">Medidas Tumba</label>
                            <input type="text" class="form-control" required id="Medidas" onkeydown="return false;">
                        </div>
                        <div class="form-group">
                            <label for="">Lote Tumba</label>
                            <input type="text" class="form-control" required id="Lote" onkeydown="return false;">
                        </div>
                        <div class="form-group">
                            <label for="">Tipo Tumba</label>
                            <input type="text" class="form-control" required id="Tipo_Tumba" onkeydown="return false;">
                        </div>
                        <div class="form-group">
                            <label for="">Fecha Apertura</label>
                            <input type="text" class="form-control" required id="Fecha_apertura" onkeydown="return false;">
                        </div>
                    </div>

                    <div class="alert alert-danger" id="Orden_mal">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>

                    <div class="alert alert-warning" id="Ingrese_Servicio">
                        <strong>¡Error!</strong> Favor de colocar el servicio para pagar
                    </div>
                    <div class="alert alert-verde" id="Orden_bien">
                        <strong>¡Genial!</strong>
                        <center>Se genero correctamente su orden de pago por favor descarguela recuerde cambiar su
                            estatus al acreditarla !Lindo Dia¡</center>
                    </div>


                    <center>
                        <button class="button" type="buttton" id="Descargar">
                            <span class="button__text">Descargar</span>
                            <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 35" id="bdd05811-e15d-428c-bb53-8661459f9307" data-name="Layer 2" class="svg">
                                    <path d="M17.5,22.131a1.249,1.249,0,0,1-1.25-1.25V2.187a1.25,1.25,0,0,1,2.5,0V20.881A1.25,1.25,0,0,1,17.5,22.131Z">
                                    </path>
                                    <path d="M17.5,22.693a3.189,3.189,0,0,1-2.262-.936L8.487,15.006a1.249,1.249,0,0,1,1.767-1.767l6.751,6.751a.7.7,0,0,0,.99,0l6.751-6.751a1.25,1.25,0,0,1,1.768,1.767l-6.752,6.751A3.191,3.191,0,0,1,17.5,22.693Z">
                                    </path>
                                    <path d="M31.436,34.063H3.564A3.318,3.318,0,0,1,.25,30.749V22.011a1.25,1.25,0,0,1,2.5,0v8.738a.815.815,0,0,0,.814.814H31.436a.815.815,0,0,0,.814-.814V22.011a1.25,1.25,0,1,1,2.5,0v8.738A3.318,3.318,0,0,1,31.436,34.063Z">
                                    </path>
                                </svg></span>
                        </button>

                    </center>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-primary">Crear Orden</button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" id="Editar_Orden" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Editar Orden</h4>
                </div>
                <div class="modal-body">
                    <form id="editar_orden" autocomplete="off">
                        <input type="hidden" id="identificador_orden">
                        <div class="alert-warning">
                            <strong>¡Aviso!</strong> La orden de pago solo se podrá editar una vez el estatus
                        </div>
                        <div class="form-grup">
                            <label for="">Estatus</label>
                            <select id="Estatus" class="form-control">
                                <option value="Corriente">Corriente</option>
                            </select>
                        </div><br>
                        <div class="form-grup">
                            <label for="">Folio Tesorería</label>
                            <input type="text" class="form-control" id="Folio" required>
                        </div>


                        <div class="alert alert-danger" id="Estatus_mal">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>

                        <div class="alert alert-warning" id="Folio_Existe">
                            <strong>¡Error!</strong> El folio ya está en uso
                        </div>
                        <div class="alert alert-verde" id="Estatus_bien">
                            <strong>¡Genial!</strong> Se actualizaron correctamente sus datos
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="sutmid" class="btn btn-primary" id="boton_editar">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Modal -->
<div class="modal fade" id="myModal_editar_titular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Historial de Orden</h4>
            </div>
            <div class="modal-body">
                <form id="Generar_Orden_respaldo">
                    <div style="display:flex; gap:10px;">
                        <div class="form-grup">
                            <label for="">Nr.Orden</label>
                            <input type="text" class="form-control" required readonly id="identificador_orden_edit">
                        </div>
                        <div class="form-grup">
                            <label for="">Folio Panteones</label>
                            <input type="text" class="form-control" required readonly id="folio_panteones">
                        </div>
                        <div class="form-grup">
                            <label for="">Folio Tesoreria</label>
                            <input type="text" class="form-control" required readonly id="folio_tesoreria_edit">
                        </div>
                    </div>

                    <br>
                    <div class="table-responsive" style="margin: none !important;">
                        <center>
                            <table id="cambio_finado" class="table-bordered table-striped" style="margin: none !important;">
                                <caption class="caption table-bordered table-striped">Historial reimpresión orden
                                </caption>
                                <thead style="margin: none !important;">
                                    <tr class="thead-tablas">
                                        <th>Nr.Orden</th>
                                        <th>Folio Tesoreria</th>
                                        <th>Responsable del <br>cambio</th>
                                        <th>Fecha Cambio</th>
                                    </tr>
                                </thead>
                                <tbody id="cambio_finado_body" style="margin: none !important;">
                                    <!-- Aquí se insertará el contenido de la tabla -->
                                </tbody>
                            </table>
                        </center>



                        <div class="alert alert-warning">
                            <strong>¡Aviso!</strong> La orden se generará con una marca indicando que es una copia de
                            seguridad y quedará registrada a su nombre.
                        </div>

                        <div class="alert alert-danger" id="respaldo_mal">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>

                        <div class="alert alert-verde" id="respaldo_bien">
                            <strong>¡Genial!</strong> Se actualizaron correctamente sus datos
                        </div>

                        <center> <button class="button" type="button" id="botssson" style="display: none;">
                                <span class="button__text">Descargar</span>
                                <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 35" id="bdd05811-e15d-428c-bb53-8661459f9307" data-name="Layer 2" class="svg">
                                        <path d="M17.5,22.131a1.249,1.249,0,0,1-1.25-1.25V2.187a1.25,1.25,0,0,1,2.5,0V20.881A1.25,1.25,0,0,1,17.5,22.131Z">
                                        </path>
                                        <path d="M17.5,22.693a3.189,3.189,0,0,1-2.262-.936L8.487,15.006a1.249,1.249,0,0,1,1.767-1.767l6.751,6.751a.7.7,0,0,0,.99,0l6.751-6.751a1.25,1.25,0,0,1,1.768,1.767l-6.752,6.751A3.191,3.191,0,0,1,17.5,22.693Z">
                                        </path>
                                        <path d="M31.436,34.063H3.564A3.318,3.318,0,0,1,.25,30.749V22.011a1.25,1.25,0,0,1,2.5,0v8.738a.815.815,0,0,0,.814.814H31.436a.815.815,0,0,0,.814-.814V22.011a1.25,1.25,0,1,1,2.5,0v8.738A3.318,3.318,0,0,1,31.436,34.063Z">
                                        </path>
                                    </svg></span>
                            </button></center><br>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="submit" class="btn btn-primary" id="GUAEDAR">Generar</button>
            </div>
            </form>
        </div>
    </div>
</div>


<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/validaciones_input.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
<script src="../js/jquery-ui.js"></script>
<script>
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
    $('#Generar_Orden_respaldo').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada
        // obtiene los datos del formulario
        var id_pago = $('#identificador_orden_edit').val();
        // crea un objeto con los datos del formulario
        var formData = {
            id_pago: id_pago
        };
        // envía los datos del formulario a través de AJAX
        // alert(JSON.stringify(formData));

        $.ajax({
            url: 'Altas/generar_respaldo.php', // reemplazar con la URL correcta
            method: 'POST', // método de envío
            data: formData,
            success: function(response) {
                // maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response == 'exito') {
                    $('#respaldo_bien').hide().addClass('alert').show();
                    $('#tbl-contact').DataTable().ajax.reload();
                    $('#GUAEDAR').hide(); // Oculta el botón de "Generar"
                    $('#botssson').show(); // Muestra el botón de "Descargar"

                } else {
                    $('#respaldo_mal').hide().addClass('alert').show();
                }
            }
        });
    });

    $('#Editar_Orden').on('hidden.bs.modal', function() {
        // Resetea el formulario dentro del modal
        $('#Estatus_bien').hide();
        $('#Folio_Existe').hide();
    });
    var Orden_bien = $('#Estatus_bien');
    var Folio_Existe = $('#Folio_Existe');
    var Estatus_mal = $('#Estatus_mal');
    var respaldo_mal = $('#respaldo_mal');
    var respaldo_bien = $('#respaldo_bien');
    var Descargar = $('#Descargar');

    // var boton = $('#botssson');
    // var guardar = $('#GUAEDAR');
    // // Ocultar mensajes de error previos

    // boton.hide();
    // guardar.show()

    respaldo_bien.hide();
    respaldo_mal.hide()
    Orden_bien.hide();
    Descargar.hide()
    Folio_Existe.hide();
    Estatus_mal.hide();
    $('#editar_orden').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // obtiene los datos del formulario
        var Estatus = $('#Estatus').val();
        var id_pago = $('#identificador_orden').val();
        var Folio = $('#Folio').val();


        // crea un objeto con los datos del formulario
        var formData = {
            Estatus: Estatus,
            id_pago: id_pago,
            Folio: Folio
        };

        // envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Editar/Estatus_orden.php', // reemplazar con la URL correcta
            method: 'POST', // método de envío
            data: formData,
            success: function(response) {
                // maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response == 'exito') {
                    $('#Estatus_bien').hide().addClass('alert').show();
                    $('#tbl-contact').DataTable().ajax.reload();
                    $('#editar_orden')[0].reset();
                    // modal
                    $('#Editar_Orden').modal('hide'); // Aquí está la corrección
                } else if (response == 'Folio_uso') {
                    $('#Folio_Existe').hide().addClass('alert').show();
                    $('#tbl-contact').DataTable().ajax.reload();
                } else {
                    $('#Estatus_mal').hide().addClass('alert').show();
                }
            }
        });
    });


    $(document).on('click', '.recuperar_datos_edit', function() {
        // Obtener los datos de la fila seleccionada
        var id_pago = $(this).data('identificador_orden');

        $('#identificador_orden').val(id_pago);
    });


    $(document).on('click', '.historial', function() {
        // Obtener los datos de la fila seleccionada
        var id_pago = $(this).data('identificador_orden');
        var teso = $(this).data('folio_tesoreria');
        var pant = $(this).data('folio_panteones');
        $('#identificador_orden_edit').val(id_pago);
        $('#folio_tesoreria_edit').val(teso);
        $('#folio_panteones').val(pant);


        $.ajax({
            url: 'Altas/recuperar_datos_historial.php',
            method: 'POST',
            data: {
                id_pago: id_pago
            },
            success: function(response) {
                $('#cambio_finado_body').html(response);
            }
        });
    });


    $('#Ordenpago').on('hidden.bs.modal', function() {
        // Resetea el formulario dentro del modal
        $('#Orden_bien').hide();
        // Resetea el formulario dentro del modal
        $('#Descargar').hide();

    });


    var Orden_bien = $('#Orden_bien');
    var Orden_mal = $('#Orden_mal');
    var Ingrese_Servicio = $('#Ingrese_Servicio');
    Orden_bien.hide();
    Orden_mal.hide();
    Ingrese_Servicio.hide();

    $('#nueva_orden').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // obtiene los datos del formulario
        var Servicio = $('#ID_SERVICIO').val();
        var folio = $('#Nr_Folio').val();
        var tumba = $('#ID_TUMBA').val();
        var Monto = $('#Monto_Orden').val();

        // crea un objeto con los datos del formulario
        var formData = {
            folio: folio,
            Monto: Monto,
            Servicio: Servicio,
            tumba: tumba
        };

        // envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Altas/Nueva_Orden.php', // reemplazar con la URL correcta
            method: 'POST', // método de envío
            data: formData,
            success: function(response) {
                // maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response == 'exito') {
                    $('#Orden_bien').hide().addClass('alert').show();
                    $('#Descargar').show();
                    $('#tbl-contact').DataTable().ajax.reload();
                    $('#nueva_orden')[0].reset();

                    // Elimina todas las filas de la tabla de servicios
                    $('#servicesTable tbody tr').remove();

                    // Puedes agregar una fila con un mensaje si es necesario
                    $('#servicesTable tbody').append(
                        '<tr id="noRecordsRow"><td colspan="5" class="no_register">No se encontraron registros</td></tr>'
                    );
                    $.get('Altas/get_folio.php', function(newFolio) {
                        // Actualiza el texto del folio en la página
                        $('#folioLabel').text('Folio: ' + newFolio);
                    });

                } else if (response == 'Ingrese_Servicio') {
                    $('#Ingrese_Servicio').hide().addClass('alert').show();
                } else {
                    $('#Orden_mal').hide().addClass('alert').show();
                }
            }
        });
    });



    $(document).ready(function() {
        var totalColumns = $('#tbl-contact thead th').length;

        $('#tbl-contact thead th').each(function(index) {
            var title = $(this).text();
            if (index < totalColumns - 5) {
                $(this).html(title +
                    '<div class="estilo_filtro"> <input type="text" class="form-control" placeholder="Buscar"/> </div>'
                );
            } else {
                $(this).html(title);
            }
        });

        var table = $('#tbl-contact').DataTable({
            "scrollX": true,
            "ordering": false,
            "pagingType": "numbers",
            "processing": true,
            "serverSide": true,
            "ajax": "../json/server_pagos.php",
            "columnDefs": [{
                    "targets": 0,
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return '<button class="none"><img src="../img/mas.png" alt="mas" class="expand-icon"></button> ' +
                            row[0];
                    }
                },
                {
                    "targets": [8, 9], // Aquí puedes especificar las columnas que quieres ocultar
                    "visible": false
                }
            ],
            "language": { //este documento funciona para cambiar el lenguaje a español
                "lengthMenu": "",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrada de MAX registros)",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros ",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                },


            }
        });

        $('#tbl-contact tbody').on('click', 'button', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).find('.expand-icon').attr('src', '../img/mas.png');
            } else {
                var rowData = row.data();
                // Aquí puedes acceder a los datos de las columnas ocultas y mostrarlos como desees
                var fechaPago = rowData[8];
                var fechaCaptura = rowData[9];
                var Fecha_limite = rowData[12];
                var Historials = rowData[13];
                row.child('<div style="display: flex;"><span class="fecha-info">Fecha de Captura: ' +
                    fechaCaptura +
                    '</span><span class="fecha-info">Limite de Pago: ' + Fecha_limite +
                    '</span><span class="fecha-info">Fecha de Pago: ' + fechaPago +
                    '</span><span class="fecha-info">Historial:<br>' + Historials +
                    '</span></div>').show();

                tr.addClass('shown');
                $(this).find('.expand-icon').attr('src', '../img/menos.png');
            }
        });

        table.columns().every(function(index) {
            if (index < totalColumns - 3) { // Si la columna no está entre las últimas tres
                var tableColumn = this;
                $('input', this.header()).on('keyup change', function() {
                    if (tableColumn.search() !== this.value) {
                        tableColumn.search(this.value).draw();
                    }
                });
            }
        });
    });













    $(function() {
        $("#buscar_tumba")
            .autocomplete({ // inicializa la función autocomplete en el elemento con ID "curso"
                source: "../json/server_recuperar_datos_tumba.php", // indica el archivo PHP donde se realizará la búsqueda
                minLength: 1, // indica la cantidad mínima de caracteres que deben ser ingresados antes de mostrar sugerencias
                select: function(event,
                    ui
                ) { // establece la función que se ejecutará cuando el usuario seleccione una sugerencia
                    event.preventDefault(); // evita que la acción predeterminada se lleve a cabo
                    // establece el valor del elemento con ID "c_lote" con el valor de la propiedad "c_lote" del objeto seleccionado

                    $('#ID_TUMBA').val(ui.item
                        .id_tumba
                    );
                    $('#Nombre_finado').val(ui.item
                        .Nombre_finado
                    ); // establece el valor del elemento con ID "id_tumbas" con el valor de la propiedad "id_tumbas" del objeto seleccionado

                    $('#Nombre_titular').val(ui.item
                        .Nombre_titular
                    );
                    $('#Telefono_titular').val(ui.item
                        .Telefono_titular
                    );
                    $('#Tipo_Tumba').val(ui.item
                        .Tipo_Tumba
                    );
                    $('#Medidas').val(ui.item
                        .Medidas
                    );
                    $('#Lote').val(ui.item
                        .Lote
                    );
                    $('#Fecha_apertura').val(ui.item
                        .Fecha_apertura
                    );
                },
            });
    });
    // Desactiva el envío del formulario al presionar Enter en todos los formularios
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            return false;
        }
    });


    $("#search").autocomplete({
        source: "../json/server_agregar_servicio.php",
        minLength: 1,
        select: function(event, ui) {

            // Verificar si ya hay un servicio en la tabla
            if ($('#servicesTable tbody tr').not('#noRecordsRow').length >= 1) {
                // Ocultar todas las alertas
                hideAllAlerts();

                // Mostrar la alerta de error
                $('#alert-duplicate').removeClass('hidden');
                return false;
            }

            // Eliminar la fila "No se encontraron registros" si está presente
            $('#noRecordsRow').remove();

            // Agregar una fila a la tabla con los detalles del servicio seleccionado
            $('#servicesTable tbody').append(
                '<tr>' +
                '<td>' + ui.item.id_servicio + '</td>' +
                '<td>' + ui.item.Nombre_Servicio + '</td>' +
                '<td>' + ui.item.Descripcion + '</td>' +
                '<td>' + ui.item.Monto + '</td>' +
                '<td><button class="btn btn-success" onclick="removeService(this)"><i class="fa-solid fa-trash"></i></button></td>' +
                '</tr>'
            );
            // Ocultar todas las alertas
            hideAllAlerts();
            // Limpiar el campo de búsqueda
            $('#Monto_Orden').val('');
            $('#ID_SERVICIO').val('');
            $('#search').val('');
            // Mostrar la alerta de servicio agregado
            $('#alert-agregar').removeClass('hidden');
            $('#ID_SERVICIO').val(ui.item.id_servicio);
            $('#Monto_Orden').val(ui.item.Monto_Orden);

            // Evitar que el autocompletado inserte el valor en el campo de búsqueda
            return false;
        }
    });



    // Función para mostrar alertas
    function showAlert(type, message) {
        $('#alerts').html('<div class="alert alert-' + type + '">' + message + '</div>');
    }


    // Función para ocultar todas las alertas
    function hideAllAlerts() {
        $('#alert-success').addClass('hidden');
        $('#alert-duplicate').addClass('hidden');
        $('#alert-removed').addClass('hidden');
        $('#alert-agregar').addClass('hidden');
    }

    // Función para eliminar un servicio de la tabla
    function removeService(button) {
        $(button).closest('tr').remove();

        // Ocultar todas las alertas
        hideAllAlerts();
        $('#Monto_Orden').val('');
        $('#ID_SERVICIO').val('');
        // Mostrar la alerta de servicio quitado
        $('#alert-removed').removeClass('hidden');

        // Si no hay más servicios en la tabla, mostrar el mensaje "No se encontraron registros"
        if ($('#servicesTable tbody tr').length === 0) {
            $('#servicesTable tbody').append(
                '<tr id="noRecordsRow"><td colspan="5" class="no_register">No se encontraron registros</td></tr>'
            );
        }
    }
</script>