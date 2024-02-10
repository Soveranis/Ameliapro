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
$query_2 = "SELECT Logo_Principal, Logo_Secundario, favicoin FROM catalogo_configuracion";
$stmt_2 = $mysqli->prepare($query_2);
$stmt_2->execute();
$stmt_2->bind_result($logoPrincipal_blob, $logoSecundario_blob, $favicoin_blob);
$stmt_2->fetch();

$logoPrincipal_data = base64_encode($logoPrincipal_blob);
$logoSecundario_data = base64_encode($logoSecundario_blob);
$favicoint_data = base64_encode($favicoin_blob);
$stmt_2->close();

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
?>
<!doctype html>
<html lang="en">

<head>
    <title>Departamento Panteones</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="" type="image/x-icon">
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
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />

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
                                        <h3>Administrador Panteones </h3>
                                        <hr><br><br>


                                        <button class="continue-application" data-toggle="modal" data-target="#Nuevo_panteon" style="margin:10px;">
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
                                            Nuevo Panteón
                                        </button>

                                        <button class="continue-application" data-toggle="modal" data-target="#agregar_lotes" style="margin-left:15px;">
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
                                            Agregar Lotes
                                        </button>


                                        <div class="table-responsive">
                                            <table id="myTable" class="table-striped table-bordered table-sm table-responsive-sm" style="width:100%;">
                                                <caption class="caption table-bordered table-striped">
                                                    Administrador de
                                                    Panteónes</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Nombre Panteón</th>
                                                        <th>Descripción</th>
                                                        <th>Responsable de <br> Captura</th>
                                                        <th>Fecha de Captura</th>
                                                        <th>Opciones</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sqsl = $mysqli->query("SELECT *
                                                      FROM mxpt_panteones AS ser
                                                      INNER JOIN mxpt_usuarios AS users ON ser.Responsable_cap = users.id_usuario");

                                                    while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                                                        <tr>
                                                            <td><?php echo $row['id_panteon']; ?></td>
                                                            <td><?php echo $row['Panteon']; ?></td>
                                                            <td><?php echo $row['Descripcion']; ?></td>
                                                            <td>
                                                                <div class="User_Resp"><?php echo $row['Usuario']; ?></div>
                                                            </td>

                                                            <td>
                                                                <div class='date'> <input type='datetime-local' class='form-control date-input-view-only' value='<?php echo $row['Fecha_Captura_Panteon']; ?>' readonly><i class='fa-regular fa-calendar caler'></i>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button class="btn editbtn" data-toggle="modal" data-target="#mymodal_editar_panteones" data-id="<?php echo htmlspecialchars($row['id_panteon']); ?>" data-nombre="<?php echo htmlspecialchars($row['Panteon']); ?>" data-descri="<?php echo htmlspecialchars($row['Descripcion']); ?>">
                                                                    Editar
                                                                </button>
                                                            </td>
                                                        </tr>


                                                    <?php } ?>

                                                </tbody>
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


<div class="modal fade bs-example" id="agregar_lotes" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Agregar Lotes</h4>
            </div>
            <div class="modal-body">
                <form id="lotes_form">
                    <div class="form-grup">
                        <label for="">Nombre Lote</label>
                        <input type="text" class="form-control" id="Nombre_lote" required>
                    </div><br>

                    <div class="form-grup">
                        <label for="">Panteón</label>
                        <select id="Panteon" class="form-control" required>
                            <option value="" Select>Seleccione un Panteón</option>
                            <?php
                            $panteon = mysqli_query($mysqli, "SELECT Panteon , Descripcion , id_panteon FROM mxpt_panteones");
                            while ($da = mysqli_fetch_array($panteon)) { ?>
                                <option name="<?php echo "" . $da['id_panteon']; ?> " value="<?php echo $da['id_panteon'] ?>">
                                    <?php echo $da['Panteon'] ?>→
                                    <?php echo $da['Descripcion'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <div class="alert alert-verde" id="lote_exitoso_edit">
                        <strong>¡Genial!</strong> Su registro se actualizó exitosamente
                    </div>
                    <div class="alert alert-warning" id="lote_existe_edit">
                        <strong>¡Errorl!</strong> El lote ya existe
                    </div>

                    <div class="alert alert-danger" id="lote_error_edit">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>


                    <div class="table-responsive">
                        <table id="Tabla_registros_lotes" class="table-striped table-bordered table-sm table-responsive-sm" style="width:100%; font-size:13px;">
                            <caption class="caption table-bordered table-striped">
                                Administrador de
                                Lotes</caption>
                            <thead>
                                <tr class="thead-tablas">
                                    <th>No.</th>
                                    <th>Panteón</th>
                                    <th>Nombre Lote</th>
                                    <th>Responsable de <br> Captura</th>
                                    <th>Fecha de Captura</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>

                        </table>

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




<!-- Modal -->
<div class="modal fade bs-example-modal-sm" id="mymodal_editar_panteones" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Editar Panteón</h4>
            </div>
            <div class="modal-body">
                <form id="editar_panteon">
                    <input type="hidden" id="id_panteon">

                    <div class="form-group">
                        <label for="">Panteón</label>
                        <div class="input-group">
                            <div class="input-group-addon">🪦</div>
                            <input type="text" class="form-control" id="pante" required onkeypress="return sololetras(event)">
                        </div>
                    </div>
                    <div class="form-grup">
                        <label for="">Descripción</label>
                        <textarea class="form-control" id="Descri" cols="30" rows="5" required onkeypress="return sololetras(event)"></textarea>
                    </div>
                    <div class="alert alert-danger" id="panteon_error_edit">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>

                    <div class="alert alert-verde" id="panteon_exitoso_edit">
                        <strong>¡Genial!</strong> Su registro se actualizo exitosamente
                    </div>
                    <div class="alert alert-warning" id="panteon_existe_edit">
                        <strong>¡Errorl!</strong> El Panteón ya existe
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sudmit" class="btn btn-primary" id="Guardar_panteon">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bs-example-modal-sm" id="Nuevo_panteon" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nuevo Panteón</h4>
            </div>
            <div class="modal-body">
                <form id="nuevo_panteon">
                    <div class="form-group">
                        <label for="">Panteón</label>
                        <div class="input-group">
                            <div class="input-group-addon">🪦</div>
                            <input type="text" class="form-control" id="panteon" required onkeypress="return sololetras(event)">
                        </div>
                    </div>
                    <div class="form-grup">
                        <label for="">Descripción</label>
                        <textarea class="form-control" id="Descripcion" cols="30" rows="5" required onkeypress="return sololetras(event)"></textarea>
                    </div>
                    <div class="alert alert-danger" id="panteon_error">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>

                    <div class="alert alert-verde" id="panteon_exitoso">
                        <strong>¡Genial!</strong> Su registro se agregó exitosamente
                    </div>
                    <div class="alert alert-warning" id="panteon_existe">
                        <strong>¡Errorl!</strong> El panteon ya existe
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




<!-- Modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" id="Editar_lote_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog  modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Editar Lote</h4>
            </div>
            <form id="formulario_editar_lote" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="idLote">
                    <input type="hidden" id="identicador">

                    <div class="form-grup">
                        <label for="">Nombre Lote</label>
                        <input type="text" class="form-control" id="nombre_lote" required>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Panteón Actual</label>
                        <input type="text" id="id_panteon_lote" class="form-control" readonly>
                        <hr>
                        <label for="">Editar</label>
                        <select id="Select_panteon" class="form-control" required>
                            <option Select value="">Panteones</option>
                            <?php
                            $panteon_editar = mysqli_query($mysqli, "SELECT Panteon , Descripcion , id_panteon FROM mxpt_panteones");
                            while ($da = mysqli_fetch_array($panteon_editar)) { ?>
                                <option name=" <?php echo "" . $da['id_panteon']; ?> " value=" <?php echo $da['id_panteon'] ?>">
                                    <?php echo $da['Panteon'] ?>→
                                    <?php echo $da['Descripcion'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <div class="alert-verde" id="Edita_lote_exito">
                        <strong>¡Genial!</strong> Su registro se actualizo exitosamente
                    </div>
                    <div class="alert-danger" id="Editar_lote_error">
                        <strong>¡Error!</strong> Algo salió mal
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

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/validaciones_input.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
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
    $(document).on('click', '.editar-btn', function() {
        var idLote = $(this).data('idlote');
        var nombre_lote = $(this).data('nombre');
        var idPanteon = $(this).data('idpanteon');
        var Pant = $(this).data('identicador');


        $('#identicador').val(Pant);
        $('#idLote').val(idLote);
        $('#nombre_lote').val(nombre_lote);
        $('#id_panteon_lote').val(idPanteon);
    });

    $(document).on('submit', '#formulario_editar_lote', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var idLote = $('#idLote').val();
        var nombreLote = $('#nombre_lote').val();
        var idPanteon = $('#Select_panteon').val();
        // Crea un objeto con los datos del formulario
        var formData = {
            idLote: idLote,
            nombreLote: nombreLote,
            idPanteon: idPanteon
        };
        // alert(JSON.stringify(formData));
        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Editar/Editar_lote.php', // Asegúrate de que esta URL sea correcta
            method: 'POST',
            data: formData,
            success: function(response) {
                $('.alert').hide();
                console.log(response);
                if (response == 'exito') {
                    $('#Edita_lote_exito').hide().addClass('alert').show();
                    $('#formulario_editar_lote')[0].reset();
                } else if (response == 'existe') {
                    // Si necesitas manejar un caso donde el lote ya existe, puedes hacerlo aquí
                } else {
                    $('#Editar_lote_error').hide().addClass('alert').show();
                }
            },
            error: function(xhr, status, error) {
                console.log('Error en la solicitud AJAX:', error);
                console.log('Respuesta completa:', xhr.responseText);
            }
        });
    });

    $(document).ready(function() {
        // Define la variable table para representar tu DataTable
        var table = $('#Tabla_registros_lotes').DataTable({
            "fnCreatedRow": function(nRow, aData, iDataIndex) {
                $(nRow).attr('id_lote', aData[0]);
            },
            'serverSide': true,
            'processing': true,
            "pageLength": 4,
            'paging': true,
            'ajax': {
                'url': '../json/server_lotes.php',
                'type': 'post',
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [1]
            }],
            'language': {
                url: '../json/json.json'
            }
        });

    });
    $(document).on('submit', '#lotes_form', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var NombreLote = $('#Nombre_lote').val();
        var Panteon = $('#Panteon').val();

        // Crea un objeto con los datos del formulario
        var formData = {
            Nombre_lote: NombreLote,
            Panteon: Panteon
        };

        // alert(JSON.stringify(formData));

        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Altas/Nuevo_Lote.php', // Cambia esto a la ruta correcta
            method: 'POST',
            data: formData,
            success: function(response) {
                $('.alert').hide();
                console.log(response);
                if (response == 'exito') {
                    $('#lote_exitoso_edit').hide().addClass('alert').show();
                    $('#lotes_form').trigger('reset');
                    $('#Tabla_registros_lotes').DataTable().ajax.reload();
                } else {
                    $('#lote_error_edit').hide().addClass('alert').show();
                }
            },
            error: function(xhr, status, error) {
                console.log('Error en la solicitud AJAX:', error);
                console.log('Respuesta completa:', xhr.responseText);
            }
        });
    });


    $(document).on('click', '.editbtn', function() {
        // Obtener los datos de la fila seleccionada
        var id_panteon = $(this).data('id');
        var Panteon = $(this).data('nombre');
        var Descripción = $(this).data('descri');

        // Mostrar los datos en una alerta
        // Establecer los valores en el formulario de edición
        $('#id_panteon').val(id_panteon);
        $('#pante').val(Panteon);
        $('#Descri').val(Descripción);

    });

    $('#lote_error_edit').hide();
    $('#lote_exitoso_edit').hide();
    $('#lote_existe_edit').hide();
    $('#Editar_lote_error').hide();
    $('#Edita_lote_exito').hide();
    var servicio_error = $('#panteon_error');
    var servicio_exitoso = $('#panteon_exitoso');
    var servicio_existe = $('#panteon_existe');
    var servicio_error_editar = $('#panteon_error_edit');
    var servicio_exitoso_editar = $('#panteon_exitoso_edit');
    var servicio_existe_editar = $('#panteon_existe_edit');

    // Ocultar mensajes de error previos
    servicio_error.hide();
    servicio_exitoso.hide();
    servicio_existe.hide();
    servicio_error_editar.hide();
    servicio_exitoso_editar.hide();
    servicio_existe_editar.hide();



    $(document).on('submit', '#editar_panteon', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var id = $('#id_panteon').val();
        var panteon = $('#pante').val();
        var Descripcion = $('#Descri').val();


        // Crea un objeto con los datos del formulario
        var formData = {
            panteon: panteon,
            id: id,
            Descripcion: Descripcion
        };
        // alert(JSON.stringify(formData));

        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Editar/Editar_panteon.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('.alert').hide();
                console.log(response);
                if (response == 'exito') {
                    $('#panteon_exitoso_edit').hide().addClass('alert').show();
                    $('#editar_panteon')[0].reset();
                    $('#Guardar_panteon').hide(); // Oculta el botón de "Generar"
                } else if (response == 'existe') {
                    $('#panteon_existe_edit').hide().addClass('alert').show();
                } else {
                    $('#panteon_error_edit').hide().addClass('alert').show();
                }

            },
            error: function(xhr, status, error) {
                console.log('Error en la solicitud AJAX:', error);
                console.log('Respuesta completa:', xhr.responseText);
            }

        });

    });


    $('#mymodal_editar_panteones').on('hidden.bs.modal', function() {
        // Resetea el formulario dentro del modal
        $('#editar_panteon')[0].reset();
        $('#Guardar_panteon').show(); // Oculta el botón de "Generar"
        // Oculta las alertas
        $('#panteon_exitoso_edit').hide();
        $('#panteon_existe_edit').hide();
        $('#panteon_error_edit').hide();
    });


    $('#Editar_lote_modal').on('hidden.bs.modal', function() {
        // Oculta las alertas
        $('#Editar_lote_error').hide();
        $('#Edita_lote_exito').hide();

    });

    $('#agregar_lotes').on('hidden.bs.modal', function() {
        // Oculta las alertas
        $('#lote_error_edit').hide();
        $('#lote_exitoso_edit').hide();

    });



    $('#Nuevo_panteon').on('hidden.bs.modal', function() {
        // Resetea el formulario dentro del modal
        $('#nuevo_panteon')[0].reset();

        // Oculta las alertas
        $('#panteon_exitoso').hide();
        $('#panteon_existe').hide();
        $('#panteon_error').hide();
    });




    $(document).on('submit', '#nuevo_panteon', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var panteon = $('#panteon').val();
        var Descripcion = $('#Descripcion').val();


        // Crea un objeto con los datos del formulario
        var formData = {
            panteon: panteon,
            Descripcion: Descripcion
        };

        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Altas/Nuevo_panteon.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('.alert').hide();
                console.log(response);
                if (response == 'exito') {
                    $('#panteon_exitoso').hide().addClass('alert').show();
                    $('#nuevo_panteon')[0].reset();
                } else if (response == 'existe') {
                    $('#panteon_existe').hide().addClass('alert').show();
                } else {
                    $('#panteon_error').hide().addClass('alert').show();
                }
            },
            error: function(xhr, status, error) {
                // Maneja el error de la solicitud AJAX
                console.log(xhr.responseText);
            }
        });

    });
    $(document).ready(
        function() {
            $('#myTable').DataTable({

                "ordering": true, //quitamos los ordenes automáticos que cuenta data tables
                "pageLength": 12, //declaramos de cuantos registros va a hacer la pagination
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

        });
</script>