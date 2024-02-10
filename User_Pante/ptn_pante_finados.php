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


$query = "SELECT Titulares, Finados, Tumbas, Reporte_General, Servicios, Panteones, Pagos FROM catalogo_modulos";
$stmt = $mysqli->prepare($query);
$stmt->execute();

// Asociar variables a los resultados de la consulta
$stmt->bind_result($Titulares, $Finados, $Tumbas, $Reporte_General, $Servicios, $Panteones, $Pagos);
// Obtener los resultados
$stmt->fetch();
?>
<!doctype html>
<html lang="en">

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
                                        <h3>Administrador Finados</h3><br><br>
                                        <hr>


                                        <button class="continue-application" data-toggle="modal"
                                            data-target="#Nuevo_finado">
                                            <div>
                                                <div class="pencil"></div>
                                                <div class="folder">
                                                    <div class="top">
                                                        <svg viewBox="0 0 24 27">
                                                            <path
                                                                d="M1,0 L23,0 C23.5522847,-1.01453063e-16 24,0.44771525 24,1 L24,8.17157288 C24,8.70200585 23.7892863,9.21071368 23.4142136,9.58578644 L20.5857864,12.4142136 C20.2107137,12.7892863 20,13.2979941 20,13.8284271 L20,26 C20,26.5522847 19.5522847,27 19,27 L1,27 C0.44771525,27 6.76353751e-17,26.5522847 0,26 L0,1 C-6.76353751e-17,0.44771525 0.44771525,1.01453063e-16 1,0 Z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="paper"></div>
                                                </div>
                                            </div>
                                            Nuevo Finado
                                        </button>



                                        <div class="table-responsive">
                                            <table class="table-bordered table-striped" name="tbl-contact"
                                                id="tbl-contact">
                                                <caption class="caption table-bordered table-striped">Administrador de
                                                    Finados</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Nombre Finado</th>
                                                        <th>Apellido Paterno Finado</th>
                                                        <th>Apellido Materno Finado</th>
                                                        <th style="	width: 110px;">Nombre Titular</th>
                                                        <th>Apellido Paterno Titular</th>
                                                        <th>Apellido Materno titular</th>
                                                        <th>Responsable</th>
                                                        <th>Fecha de Inhumación</th>
                                                        <th>Fecha de Captura</th>
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


<!-- Opciones -->
<div class="modal fade" id="opciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body">
                <div class="conten_imagenes_opciones">
                    <div class="form-grup">

                        <img src="../img/male-user-edit_25348.png" class="opciones" alt="">
                        <button class="continue-application" data-toggle="modal" data-dismiss="modal"
                            data-target="#Editar_finado_modal" style="margin-top:10px; height: 49px;">
                            <div>
                                <div class="pencil"></div>
                                <div class="folder">
                                    <div class="top">
                                        <svg viewBox="0 0 24 27">
                                            <path
                                                d="M1,0 L23,0 C23.5522847,-1.01453063e-16 24,0.44771525 24,1 L24,8.17157288 C24,8.70200585 23.7892863,9.21071368 23.4142136,9.58578644 L20.5857864,12.4142136 C20.2107137,12.7892863 20,13.2979941 20,13.8284271 L20,26 C20,26.5522847 19.5522847,27 19,27 L1,27 C0.44771525,27 6.76353751e-17,26.5522847 0,26 L0,1 C-6.76353751e-17,0.44771525 0.44771525,1.01453063e-16 1,0 Z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="paper"></div>
                                </div>
                            </div>
                            Editar Finado
                        </button>

                    </div>
                    <div class="form-grup">

                        <img src="../img/change-contract-owner.png.img.png" class="opciones" alt="">
                        <button class="continue-application" style="margin-top:10px; height: 49px;" data-toggle="modal"
                            data-dismiss="modal" data-target="#cambio_titular">
                            <div>
                                <div class="pencil"></div>
                                <div class="folder">
                                    <div class="top">
                                        <svg viewBox="0 0 24 27">
                                            <path
                                                d="M1,0 L23,0 C23.5522847,-1.01453063e-16 24,0.44771525 24,1 L24,8.17157288 C24,8.70200585 23.7892863,9.21071368 23.4142136,9.58578644 L20.5857864,12.4142136 C20.2107137,12.7892863 20,13.2979941 20,13.8284271 L20,26 C20,26.5522847 19.5522847,27 19,27 L1,27 C0.44771525,27 6.76353751e-17,26.5522847 0,26 L0,1 C-6.76353751e-17,0.44771525 0.44771525,1.01453063e-16 1,0 Z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="paper"></div>
                                </div>
                            </div>
                            Cambiar Titular
                        </button>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<!--Cambio de finado -->
<div class="modal fade" tabindex="-1" id="cambio_titular" role="dialog" aria-labelledby="myModalLabel"
    style="overflow:auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cambio Titular</h4>
            </div>
            <form id="cambio_titular_modal" autocomplete="off">
                <div class="modal-body">
                    <div class="form-grup">
                        <label for="">Titular Actual</label>
                        <input type="text" class="form-control " id="NombreTITULAR" readonly="">
                        <input type="hidden" id="ID_FINADO">
                        <input type="hidden" id="TITULAAR">
                    </div>
                    <div class="cont-madre">

                        <label for="">Buscar Titular</label>
                        <div class="form">
                            <label for="search">
                                <input required="" autocomplete="off" placeholder="Buscar Titular" id="search"
                                    class="Buscar_titular_remplaso" type="text">
                                <div class="icon_buscador">
                                    <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round"
                                            stroke-linecap="round"></path>
                                    </svg>

                                </div>

                            </label>
                        </div>
                        <div class="Datos_titular">
                            <input type="hidden" name="id_titular" id="id_titular_nuevo">
                            <div class="form-grup">
                                <label for="">Nr.</label>
                                <input type="text" class="form-control busc" id="Nr" required onkeydown="return false;"
                                    ed>
                            </div>
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" class="form-control busc " id="Nombre_titular_edit" required
                                    onkeydown="return false;">
                            </div>
                            <div class="form-grup">
                                <label for="">Telefono</label>
                                <input type="text" class="form-control  busc" id="Telefono_titular_edit" required
                                    onkeydown="return false;">
                            </div>

                        </div>
                        <div class="form-grup">
                            <label for="">Direccion</label>

                            <textarea class="form-control " id="Direccion_edit" cols="30" rows="2" required
                                onkeydown="return false;"></textarea>
                        </div>

                        <div class="alert-verde" id="exito_cambio_titular">
                            <strong>¡Genial!</strong> Su titular se actualizó exitosamente
                        </div>
                        <div class="alert-danger" id="error_cambio_titular">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera
                            las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>



                        <div class="table-responsive" style="margin: none !important;">
                            <center>
                                <table id="cambio_finado" class="table-bordered table-striped"
                                    style="margin: none !important;">
                                    <caption class="caption table-bordered table-striped">Titulares Pasados
                                    </caption>
                                    <thead style="margin: none !important;">
                                        <tr class="thead-tablas">
                                            <th>Nombre Titular</th>
                                            <th>Nombre Finado</th>
                                            <th>Responsable del <br>cambio</th>
                                            <th>Fecha Cambio</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cambio_finado_body" style="margin: none !important;">
                                        <!-- Aquí se insertará el contenido de la tabla -->
                                    </tbody>
                                </table>
                            </center>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="sudmit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Nuevo Finado -->
<div class="modal fade" tabindex="-1" id="Nuevo_finado" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nuevo Finado</h4>
            </div>
            <form id="Nuevo_finado_alta" autocomplete="off">
                <div class="modal-body">
                    <div class="cont-madre">

                        <div class="form-grup">
                            <label for="">Nombre finado</label>
                            <input type="text" class="form-control" id="Nombre_finado" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>
                        <div class="form-grup">
                            <label for="">Apellido Paterno</label>
                            <input type="text" class="form-control" id="Apellido_Paterno" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>
                        <div class="form-grup">
                            <label for="">Apellido Materno</label>
                            <input type="text" class="form-control" id="Apellido_Materno" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>

                        <label for="">Buscar Titular</label>
                        <div class="form">
                            <label for="search">
                                <input required="" autocomplete="off" placeholder="Buscar Titular"
                                    id="Buscar_Nuevo_titular_Alta" type="text">
                                <div class="icon_buscador">
                                    <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round"
                                            stroke-linecap="round"></path>
                                    </svg>

                                </div>

                            </label>
                        </div>
                        <div class="Datos_titular">
                            <input type="hidden" name="id_titular" id="id_titular">
                            <div class="form-grup">
                                <label for="">Nr.</label>
                                <input type="text" class="form-control busc" id="Nr_nuevo" onkeydown="return false;"
                                    readonly>
                            </div>
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" class="form-control busc" id="Nombre_titular"
                                    onkeydown="return false;" readonly>
                            </div>
                            <div class="form-grup">
                                <label for="">Teléfono</label>
                                <input type="text" class="form-control busc" id="Telefono_titular"
                                    onkeydown="return false;" readonly>
                            </div>

                        </div>
                        <textarea class="form-control " id="Direccion_alta" cols="30" rows="2" required
                            onkeydown="return false;"></textarea>

                        <div class="form-grup">
                            <label for="">Fecha inhumación</label>
                            <input type="date" class="form-control" id="Fecha_inhumacion" required>
                        </div>

                        <div class="alert-verde" id="Nuevo_finado_exito">
                            <strong>¡Genial!</strong> Su registro se agregó exitosamente
                        </div>
                        <div class="alert-danger" id="Nuevo_finado_error">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera
                            las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="sudmit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>





<!-- Editar finado -->
<div class="modal fade bs-example-modal-sm" id="Editar_finado_modal" tabindex="-1" role="dialog"
    aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Editar Finado</h4>
            </div>
            <form id="Editar_finado">
                <div class="modal-body">
                    <div class="cont-madre">
                        <input type="hidden" name="id_finado" id="ID_FINADO_2">
                        <div class="form-grup">
                            <label for="">Nombre finado</label>
                            <input type="text" class="form-control" id="NombreFINADO" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>
                        <div class="form-grup">
                            <label for="">Apellido Paterno</label>
                            <input type="text" class="form-control" id="ApellidoPaternoFINADO" name="finados" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>

                        <div class="form-grup">
                            <label for="">Apellido Materno</label>
                            <input type="text" class="form-control" id="ApellidoMaternoFINADO" name="finados" required
                                onkeypress="return sololetras(event)" maxlength="30">
                        </div>

                        <div class="form-grup">
                            <label for="">Fecha inhumación</label>
                            <input type="date" name="fecha_inhumacion" class="form-control" id="FechaINHUMACION"
                                required>
                        </div>

                        <div class="alert-verde" id="Editar_finado_exito">
                            <strong>¡Genial!</strong> Su registro se actualizó exitosamente
                        </div>
                        <div class="alert-danger" id="Editar_finado_error">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera
                            las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="sudmit" class="btn btn-primary" id="Guardar_finado">Guardar</button>
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
$('#Editar_finado_modal').on('hidden.bs.modal', function() {
    // Resetea el formulario dentro del modal
    $('#Editar_finado')[0].reset();
    $('#Guardar_finado').show(); // Oculta el botón de "Generar"
    // Oculta las alertas
    $('#Editar_finado_error').hide();
    $('#Editar_finado_exito').hide();

});
$('#Nuevo_finado').on('hidden.bs.modal', function() {
    // Resetea el formulario dentro del modal
    $('#Nuevo_finado_exito').hide();
    $('#Nuevo_finado_error').hide();

});

$(document).on('click', '.edit_finado', function() {
    // Obtener los datos de la fila seleccionada
    var id_finado = $(this).data('id');
    var Nombre = $(this).data('nombre');
    var ape = $(this).data('apellido');
    var Fecha = $(this).data('fecha_inhumacion');

    // Mostrar los datos en una alerta
    // Establecer los valores en el formulario de edición

    $('#Identificador').val(id_finado);
    $('#nombress').val(Nombre);
    $('#Apellido').val(ape);
    $('#Fecha').val(Fecha);
});
$(document).on('click', '.btn.recuperar_datos', function() {
    var idFinado = $(this).data('axel');
    var idFinado_2 = $(this).data('id_finado_2');
    var titular = $(this).data('titular');
    var nombreTitular = $(this).data('nombre');
    var fechaInhumacion = $(this).data('fecha');
    var nombreFinado = $(this).data('nombre-finado');
    var apellidoPaternoFinado = $(this).data('apellido-paterno-finado');
    var apellidoMaternoFinado = $(this).data('apellido-materno-finado');

    $('#TITULAAR').val(titular);
    $('#ID_FINADO').val(idFinado);
    $('#ID_FINADO_2').val(idFinado_2);
    $('#NombreTITULAR').val(nombreTitular);
    $('#FechaINHUMACION').val(fechaInhumacion);
    $('#NombreFINADO').val(nombreFinado);
    $('#ApellidoPaternoFINADO').val(apellidoPaternoFinado);
    $('#ApellidoMaternoFINADO').val(apellidoMaternoFinado);

    $.ajax({
        url: 'Altas/recuperar_datos.php',
        method: 'POST',
        data: {
            idFinado: idFinado
        },
        success: function(response) {
            $('#cambio_finado_body').html(response);
        }
    });
});


$('#Nuevo_finado_alta').on('submit', function(event) {
    event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

    // obtiene los datos del formulario
    var Nombre_finado = $('#Nombre_finado').val();
    var id_titular = $('#id_titular').val();
    var Fecha_inhumacion = $('#Fecha_inhumacion').val();
    var Apellido_Materno = $('#Apellido_Materno').val();
    var Apellido_Paterno = $('#Apellido_Paterno').val();

    // crea un objeto con los datos del formulario
    var formData = {
        Nombre_finado: Nombre_finado,
        id_titular: id_titular,
        Apellido_Paterno: Apellido_Paterno,
        Apellido_Materno: Apellido_Materno,
        Fecha_inhumacion: Fecha_inhumacion
    };

    // envía los datos del formulario a través de AJAX
    $.ajax({
        url: 'Altas/Nuevo_finado.php', // reemplazar con la URL correcta
        method: 'POST', // método de envío
        data: formData,
        success: function(response) {
            // maneja la respuesta del servidor después de enviar los datos del formulario
            console.log(response);
            $('.alert').hide();
            if (response == 'exito') {
                $('#Nuevo_finado_exito').hide().addClass('alert').show();
                // Recargar la tabla
                $('#tbl-contact').DataTable().ajax.reload();
                // Restablecer el formulario


                $('#Nuevo_finado_alta')[0].reset();
            } else {
                $('#Nuevo_finado_error').hide().addClass('alert').show();
            }
        }
    });
});


$(document).on('submit', '#Editar_finado', function(event) {
    event.preventDefault(); // evita que el formulario se envíe de forma predeterminada


    var id_finado = $('#ID_FINADO_2').val();
    var nombre_finado = $('#NombreFINADO').val();
    var ApellidoMaternoFINADO = $('#ApellidoMaternoFINADO').val();
    var ApellidoPaternoFINADO = $('#ApellidoPaternoFINADO').val();
    var fecha_inhumacion = $('#FechaINHUMACION').val();

    // crea un objeto con los datos del formulario
    var formData = {

        id_finado: id_finado,
        nombre_finado: nombre_finado,
        ApellidoMaternoFINADO: ApellidoMaternoFINADO,
        ApellidoPaternoFINADO: ApellidoPaternoFINADO,
        fecha_inhumacion: fecha_inhumacion
    };

    // muestra el contenido de los datos en un alert
    // alert(JSON.stringify(formData));

    // envía los datos del formulario a través de AJAX
    $.ajax({
        url: 'Editar/Editar_finado.php', // reemplazar con la URL correcta
        method: 'POST', // método de envío
        data: formData,
        success: function(response) {
            // maneja la respuesta del servidor después de enviar los datos del formulario
            console.log(response);
            $('.alert').hide();
            if (response == 'exito') {
                $('#Editar_finado_exito').hide().addClass('alert').show();
                // Recargar la tabla
                $('#tbl-contact').DataTable().ajax.reload();
                // Vacía un campo de entrada específico por su ID

                $('#Editar_finado')[0].reset();
                $('#Guardar_finado').hide(); // Oculta el botón de "Generar"
            } else {
                $('#Editar_finado_error').hide().addClass('alert').show();
            }
        }
    });
});



$('#cambio_titular_modal').on('submit', function(event) {
    event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

    // obtiene los datos del formulario

    var finado = $('#ID_FINADO').val();
    var id_titular_actual = $('#TITULAAR').val();
    var titular_nuevo = $('#id_titular_nuevo').val();

    // crea un objeto con los datos del formulario
    var formData = {
        id_titular_actual: id_titular_actual,
        titular_nuevo: titular_nuevo,
        finado: finado,
    };
    $.ajax({
        url: 'Editar/cambio_titular.php', // reemplazar con la URL correcta
        method: 'POST', // método de envío
        data: formData,
        success: function(response) {
            // maneja la respuesta del servidor después de enviar los datos del formulario
            console.log(response);
            $('.alert').hide();
            if (response == 'exito_cambio_titular') {
                $('#exito_cambio_titular').hide().addClass('alert').show();
                // Recargar la tabla
                setTimeout(function() {
                    $('#exito_cambio_titular').hide();
                }, 3000);
                $('.Buscar_titular_remplaso').val('');
                $('#Nr').val('');
                $('#Nombre_titular_edit').val('');
                $('#Telefono_titular_edit').val('');
                $('#Direccion_edit').val('');
                $('#tbl-contact').DataTable().ajax.reload();
            } else {
                $('#error_cambio_titular').hide().addClass('alert').show();
            }
        }
    });
    // envía los datos del formulario a través de AJAX
    // alert(JSON.stringify(formData));

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


var Nuevo_finado_error = $('#Nuevo_finado_error');
var Nuevo_finado_exito = $('#Nuevo_finado_exito');
var Editar_finado_error = $('#Editar_finado_error');
var Editar_finado_exito = $('#Editar_finado_exito');
var exito_cambio_titular = $('#exito_cambio_titular');
var error_cambio_titular = $('#error_cambio_titular');
// Ocultar mensajes de error previos
// Ocultar mensajes de error previos
Nuevo_finado_error.hide();
Nuevo_finado_exito.hide();
Editar_finado_error.hide();
Editar_finado_exito.hide();
exito_cambio_titular.hide();
error_cambio_titular.hide();
$(function() {
    $("#Buscar_Nuevo_titular_Alta")
        .autocomplete({ // inicializa la función autocomplete en el elemento con ID "curso"
            source: "../json/server_recuperar_datos.php", // indica el archivo PHP donde se realizará la búsqueda
            minLength: 1, // indica la cantidad mínima de caracteres que deben ser ingresados antes de mostrar sugerencias
            select: function(event,
                ui
            ) { // establece la función que se ejecutará cuando el usuario seleccione una sugerencia
                event.preventDefault(); // evita que la acción predeterminada se lleve a cabo
                $('#Telefono_titular').val(ui.item
                    .Telefono_titular
                ); // establece el valor del elemento con ID "c_lote" con el valor de la propiedad "c_lote" del objeto seleccionado
                $('#Nombre_titular').val(ui.item
                    .Nombre_titular
                ); // establece el valor del elemento con ID "id_tumbas" con el valor de la propiedad "id_tumbas" del objeto seleccionado

                $('#Direccion_alta').val(ui.item
                    .Direccion
                );
                $('#Nr_nuevo').val(ui.item
                    .Nr
                );


                $('#id_titular').val(ui.item
                    .id_titular
                ); // establece el valor del elemento con ID "c_medida" con el valor de la propiedad "c_medida" del objeto seleccionado
            }
        });
});

$(function() {
    $(".Buscar_titular_remplaso")
        .autocomplete({ // inicializa la función autocomplete en el elemento con ID "curso"
            source: "../json/server_recuperar_datos.php", // indica el archivo PHP donde se realizará la búsqueda
            minLength: 1, // indica la cantidad mínima de caracteres que deben ser ingresados antes de mostrar sugerencias
            select: function(event,
                ui
            ) { // establece la función que se ejecutará cuando el usuario seleccione una sugerencia
                event.preventDefault(); // evita que la acción predeterminada se lleve a cabo
                $('#Telefono_titular_edit').val(ui.item
                    .Telefono_titular
                ); // establece el valor del elemento con ID "c_lote" con el valor de la propiedad "c_lote" del objeto seleccionado
                $('#Nombre_titular_edit').val(ui.item
                    .Nombre_titular
                ); // establece el valor del elemento con ID "id_tumbas" con el valor de la propiedad "id_tumbas" del objeto seleccionado

                $('#Direccion_edit').val(ui.item
                    .Direccion
                );
                $('#Nr').val(ui.item
                    .Nr
                );

                $('#id_titular_nuevo').val(ui.item
                    .id_titular
                ); // establece el valor del elemento con ID "c_medida" con el valor de la propiedad "c_medida" del objeto seleccionado
            }
        });
});




$(document).ready(function() {
    var totalColumns = $('#tbl-contact thead th').length;

    $('#tbl-contact thead th').each(function(index) {
        var title = $(this).text();
        if (index < totalColumns - 2) { // Si la columna no está entre las últimas tres
            $(this).html(title +
                '<div class="estilo_filtro"> <input type="text" class="form-control" placeholder="Buscar"/> </div>'
            );
        } else {
            $(this).html(title); // Simplemente agregamos el título sin el filtro
        }
    });



    var table = $('#tbl-contact').DataTable({
        "scrollX": true,
        "ordering": true,
        "pagingType": "numbers",
        "processing": true,
        "serverSide": true,
        "ajax": "../json/server_finados.php",
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

    $('#tbl-contact tbody').on('click', '.none', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $(this).find('.expand-icon').attr('src', '../img/mas.png');
        } else {
            var rowData = row.data();
            // Aquí puedes acceder a los datos de las columnas ocultas y mostrarlos como desees
            var Fecha_inhumacion = rowData[8];
            var Fecha_Captura_Finado = rowData[9];
            var Opciones = rowData[10];
            row.child('<div style="display: flex;"><span class="fecha-info">Fecha de inhumacion: ' +
                Fecha_inhumacion +
                '</span><span class="fecha-info">Fecha de Captura: ' +
                Fecha_Captura_Finado + '</span><span class="fecha-info">Opciones: ' +
                Opciones + '</span></div>').show();
            tr.addClass('shown');
            $(this).find('.expand-icon').attr('src', '../img/menos.png');
        }
    });


    table.columns().every(function(index) {
        if (index < totalColumns - 2) { // Si la columna no está entre las últimas tres
            var tableColumn = this;
            $('input', this.header()).on('keyup change', function() {
                if (tableColumn.search() !== this.value) {
                    tableColumn.search(this.value).draw();
                }
            });
        }
    });

});
</script>