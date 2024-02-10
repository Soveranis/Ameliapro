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
    <title>Departamento Panteones</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/main.css">
    <?php
    if (isset($favicoin_blob) && !empty($favicoin_blob)) {
        $base64Image = base64_encode($favicoin_blob);
    } else {
        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
        $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
    } ?>
    <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />

</head>

<body>
    <div id="wrapper">
        <!-- Agregamos el menu lateral con los links respectivos -->
        <?php include('../Vistas/Menu_Lateral_user_pante.php'); ?>


        <div class="main">
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="demo-area-chart" class="ct-chart">
                                        <h3>Administrador Servicios</h3><br>
                                        <hr>

                                        <br><br>
                                        <button class="continue-application" data-toggle="modal" style="margin: 15px;" data-target="#mymodal_nuevo_servicios">
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
                                            Nuevo Servicio
                                        </button>
                                        <button class="continue-application" style="margin: 15px;" data-toggle="modal" data-target="#Historial_Servicios" style="margin-left:15px;">
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
                                            Historico
                                        </button>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table id="myTable" class="table-striped table-bordered table-sm table-responsive-sm" style="width:100%;">
                                                        <caption class="caption table-bordered table-striped">
                                                            Administrador de
                                                            Servicios</caption>
                                                        <thead>
                                                            <tr class="thead-tablas">
                                                                <th>No.</th>
                                                                <th>Nombre Servicio</th>
                                                                <th>Descripción</th>
                                                                <th>Monto</th>
                                                                <th>Responsable de <br> Captura</th>
                                                                <th>Fecha de Captura</th>
                                                                <th>Opciones</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sqsl = $mysqli->query("SELECT *
                                                      FROM mxpt_servicios AS ser
                                                      INNER JOIN mxpt_usuarios AS users ON ser.Responsable_cap = users.id_usuario");

                                                            while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                                                                <tr>
                                                                    <td><?php echo $row['id_servicio']; ?></td>
                                                                    <td><?php echo $row['Nombre_Servicio']; ?></td>
                                                                    <td><?php echo $row['Descripcion']; ?></td>
                                                                    <td><?php echo $row['Monto']; ?></td>
                                                                    <td>
                                                                        <div class="User_Resp">
                                                                            <?php echo $row['Usuario']; ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class='date'> <input type='datetime-local' class='form-control date-input-view-only' value='<?php echo $row['Fecha_Captura_Servicio']; ?>' readonly><i class='fa-regular fa-calendar caler'></i>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button class="btn editbtn" data-toggle="modal" data-target="#mymodal_editar_servicios" data-id="<?php echo htmlspecialchars($row['id_servicio']); ?>" data-identificador="<?php echo htmlspecialchars($row['Nombre_Servicio']); ?>" data-pronombre="<?php echo htmlspecialchars($row['Descripcion']); ?>" data-costo="<?php echo htmlspecialchars($row['Monto']); ?>">
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
            </div>
        </div>
        <!-- Incluimos el Menu celular -->
        <?php include('../Vistas/Menu_celular.php'); ?>
        <!-- Incluimos el footer -->
        <?php include('../Vistas/Footer.php'); ?>
    </div>




    <div class="modal fade bs-example-modal-lg" id="Historial_Servicios" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Servicio</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="myTable_Historial" class="table-striped table-bordered table-sm table-responsive-sm" style="width:100%;">
                            <caption class="caption table-bordered table-striped">
                                Historico</caption>
                            <thead>
                                <tr class="thead-tablas">
                                    <th>No.</th>
                                    <th>Nombre Servicio</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Responsable de <br> Cambio</th>
                                    <th>Fecha de cambio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqsl = $mysqli->query("SELECT *
                                      FROM catalogo_historico_servicios AS ser
                                      INNER JOIN mxpt_usuarios AS users ON ser.Responsable_cambio = users.id_usuario");
                                while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo $row['id_servicio_cambio']; ?></td>
                                        <td><?php echo $row['Nombre_Servicio_cambio']; ?></td>
                                        <td><?php echo $row['Descripcion_cambio']; ?></td>
                                        <td><?php echo $row['Monto_cambio']; ?></td>
                                        <td>
                                            <div class="User_Resp">
                                                <?php echo $row['Usuario']; ?></div>
                                        </td>
                                        <td>
                                            <div class='date'> <input type='datetime-local' class='form-control date-input-view-only' value='<?php echo $row['Fecha_cambio']; ?>' readonly><i class='fa-regular fa-calendar caler'></i>
                                            </div>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="mymodal_nuevo_servicios" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Nuevo Servicio</h4>
                </div>
                <form id="NVO_SERVICIO" autocomplete="off">
                    <div class="modal-body">
                        <div class="cont-madre">

                            <div class="form-grup">
                                <label for="">Nombre del Servicio</label>
                                <input type="text" class="form-control" id="nombre_Servicio" required onkeypress="return sololetras(event)" maxlength="40">
                            </div>
                            <div class="form-grup">
                                <label for="">Descripción del Servicio</label>
                                <textarea class="form-control" id="descrip_Servicio" cols="4" rows="4" onkeypress="return sololetras(event)" required maxlength="40"></textarea>
                            </div>
                            <div class="form-grup">
                                <label for="">Monto</label>
                                <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    <input type="text" class="form-control" id="Monto" onkeypress="return solonumeros(event)" required>

                                </div>
                            </div>

                        </div>
                        <div class="alert alert-danger" id="servicio_error">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>

                        <div class="alert alert-verde" id="servicio_exitoso">
                            <strong>¡Genial!</strong> Su registro se agregó exitosamente
                        </div>
                        <div class="alert alert-warning" id="servicio_existe">
                            <strong>¡Errorl!</strong> El servicio ya existe
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="mymodal_editar_servicios" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Editar Servicio</h4>
                </div>
                <form id="edit" autocomplete="off">
                    <div class="modal-body">
                        <div class="cont-madre">
                            <input type="hidden" id="id" name="id_servicio_editar">
                            <div class="form-group">
                                <label for="Servicio">Nombre del servicio:</label>
                                <input type="text" class="form-control" id="Servicio" name="nombre_servicio" onkeypress="return sololetras(event)">
                            </div>
                            <div class="form-group">
                                <label for="Descri">Descripción:</label>
                                <textarea class="form-control" id="Descri" name="descripcion" onkeypress="return sololetras(event)"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="Costo">Monto:</label>
                                <input type="text" class="form-control" id="Costo" name="monto" onkeypress="return solonumeros(event)">
                            </div>

                        </div>
                        <div class="alert alert-danger" id="servicio_error_editar">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>

                        <div class="alert alert-verde" id="servicio_exitoso_editar">
                            <strong>¡Genial!</strong> Su registro se actualizo exitosamente
                        </div>
                        <div class="alert alert-warning" id="servicio_existe_editar">
                            <strong>¡Errorl!</strong> El servicio ya existe
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                        <button type="submit" class="btn btn-primary" id="Guardar_sevicio">Guardar</button>
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
        $(document).ready(
            function() { //creamos una función usando datatables para poder hacer un crud este crud es diferente que los demás mas fácil pero tiene la desventaja que si lo hacemos con muchos registros se va a trabar este es perfecto para tablas que no superen los 2000 registros
                $('#myTable_Historial').DataTable({
                    "orderFixed": [
                        [0, "asc"]
                    ], //declaramos que el orden sea de en numérico del 1 hasta donde sean los registros
                    "ordering": false, //quitamos los ordenes automáticos que cuenta data tables
                    "pageLength": 5, //declaramos de cuantos registros va a hacer la pagination
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
        $(document).on('click', '.editbtn', function() {
            // Obtener los datos de la fila seleccionada
            var id_servicio = $(this).data('id');
            var Nombre = $(this).data('identificador');
            var Descripcion = $(this).data('pronombre');
            var Monto = $(this).data('costo');

            // Establecer los valores en el formulario de edición
            $('#id').val(id_servicio);
            $('#Servicio').val(Nombre);
            // Establecer los valores en el formulario de edición
            $('#Descri').val(Descripcion);
            $('#Costo').val(Monto);
        });

        $(document).on('submit', '#edit', function(event) {
            event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

            // Obtén los datos del formulario
            var id = $('#id').val();
            var nombreServicio = $('#Servicio').val();
            var descripcionServicio = $('#Descri').val();
            var monto = $('#Costo').val();

            // Crea un objeto con los datos del formulario
            var formData = {
                id: id,
                nombre_servicio: nombreServicio,
                descripcion_servicio: descripcionServicio,
                monto: monto
            };

            // Envía los datos del formulario a través de AJAX
            $.ajax({
                url: 'Editar/Edit_servicios.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Maneja la respuesta del servidor después de enviar los datos del formulario
                    console.log(response);

                    $('.alert').hide();
                    console.log(response);
                    if (response == 'exito') {
                        $('#servicio_exitoso_editar').hide().addClass('alert').show();
                        $('#edit')[0].reset();
                        $('#Guardar_sevicio').hide(); // Oculta el botón de "Generar"
                    } else if (response == 'existe') {
                        $('#servicio_existe_editar').hide().addClass('alert').show();
                    } else {
                        $('#servicio_error_editar').hide().addClass('alert').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Maneja el error de la solicitud AJAX
                    console.log(xhr.responseText);
                }
            });
        });

        $('#mymodal_editar_servicios').on('hidden.bs.modal', function() {
            // Resetea el formulario dentro del modal
            $('#edit')[0].reset();
            $('#Guardar_sevicio').show(); // Oculta el botón de "Generar"
            // Oculta las alertas
            $('#servicio_exitoso_editar').hide();
            $('#servicio_existe_editar').hide();
            $('#servicio_error_editar').hide();
        });


        $('#mymodal_nuevo_servicios').on('hidden.bs.modal', function() {
            // Resetea el formulario dentro del modal
            $('#NVO_SERVICIO')[0].reset();

            // Oculta las alertas
            $('#servicio_exitoso').hide();
            $('#servicio_existe').hide();
            $('#servicio_error').hide();
        });








        $('#NVO_SERVICIO').on('submit', function(event) {
            event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

            // obtiene los datos del formulario
            var nombre_Servicio = $('#nombre_Servicio').val();
            var descrip_Servicio = $('#descrip_Servicio').val();
            var Monto = $('#Monto').val();

            // crea un objeto con los datos del formulario
            var formData = {
                nombre_Servicio: nombre_Servicio,
                descrip_Servicio: descrip_Servicio,
                Monto: Monto
            };

            // envía los datos del formulario a través de AJAX
            $.ajax({
                url: 'Altas/Nuevo_servicio.php', // reemplazar con la URL correcta
                method: 'POST', // método de envío
                data: formData,
                success: function(response) {
                    // maneja la respuesta del servidor después de enviar los datos del formulario
                    console.log(response);
                    $('.alert').hide();
                    if (response == 'exito') {
                        $('#servicio_exitoso').hide().addClass('alert').show();
                        // Restablecer el formulario
                        $('#NVO_SERVICIO')[0].reset();
                    } else if (response == 'existe') {
                        $('#servicio_existe').hide().addClass('alert').show();
                    } else {
                        $('#servicio_error').hide().addClass('alert').show();
                    }
                }
            });
        });



        var servicio_error = $('#servicio_error');
        var servicio_exitoso = $('#servicio_exitoso');
        var servicio_existe = $('#servicio_existe');
        var servicio_error_editar = $('#servicio_error_editar');
        var servicio_exitoso_editar = $('#servicio_exitoso_editar');
        var servicio_existe_editar = $('#servicio_existe_editar');

        // Ocultar mensajes de error previos
        servicio_error.hide();
        servicio_exitoso.hide();
        servicio_existe.hide();
        servicio_error_editar.hide();
        servicio_exitoso_editar.hide();
        servicio_existe_editar.hide();
        $(document).ready(
            function() { //creamos una función usando datatables para poder hacer un crud este crud es diferente que los demás mas fácil pero tiene la desventaja que si lo hacemos con muchos registros se va a trabar este es perfecto para tablas que no superen los 2000 registros
                $('#myTable').DataTable({
                    "autoFill": true,
                    "orderFixed": [
                        [0, "asc"]
                    ], //declaramos que el orden sea de en numérico del 1 hasta donde sean los registros
                    "ordering": false, //quitamos los ordenes automáticos que cuenta data tables
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
</body>

</html>