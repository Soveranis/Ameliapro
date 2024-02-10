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
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <?php
    if (isset($favicoin_blob) && !empty($favicoin_blob)) {
        $base64Image = base64_encode($favicoin_blob);
    } else {
        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
        $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
    } ?>
    <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" type="image/x-icon">
    <link rel="stylesheet" href="../css/Buscador.css">

    <style>
        #tbl-contact_filter {
            display: none;
        }

        .ui-autocomplete li {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .ui-autocomplete li:last-child {
            border-bottom: none;
        }

        .ui-autocomplete li small {
            font-size: 12px;
        }
    </style>

    <!-- Ahora los scripts -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-ui.js"></script> <!-- jQuery UI debe cargarse después de jQuery -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/validaciones_input.js"></script>
    <script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
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
                                        <h3>Administador Tumbas</h3>
                                        <hr><br><br>


                                        <button class="continue-application" data-toggle="modal" data-target="#Opciones_nueva_tumba">
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
                                            Generar Nueva Tumba
                                        </button>


                                        <div class="table-responsive">
                                            <table class="table-bordered table-striped" name="tbl-contact" id="tbl-contact">
                                                <caption class="caption table-bordered table-striped">
                                                    Administrador de
                                                    Tumbas</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.Tumba</th>
                                                        <th>Titular</th>
                                                        <th>Apellido Paterno</th>
                                                        <th>Apellido Materno</th>
                                                        <th>Finado</th>
                                                        <th>Apellido Paterno</th>
                                                        <th>Apellido Materno</th>
                                                        <th>Panteones</th>
                                                        <th>Lote</th>
                                                        <th>Estatus</th>
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
<div class="modal fade" id="Opciones_nueva_tumba" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body">

                <div class="form-grup">
                    <div class="bs-callout bs-callout-warning" id="callout-stacked-modals">
                        <h4>¡Aviso!</h4> Si la tumba deseada no existe en sus registros use este método el cual creara
                        la tumba desde cero contando los 7 años de perpetuidad
                        <hr>
                        <div class="toggler">
                            <input id="toggler-1" name="toggler-1" type="checkbox" value="1">
                            <label for="toggler-1">
                                <svg class="toggler-on" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                    <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5"></polyline>
                                </svg>
                                <svg class="toggler-off" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                    <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                                    <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
                                </svg>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-grup">
                    <div class="bs-callout bs-callout-danger" id="callout-stacked-modals">
                        <h4>¡Aviso!</h4> Si la tumba deseada existe en sus registros use este método el cual dejara
                        ingresar sus datos actualizados al sistema
                        <hr>
                        <div class="toggler">
                            <input id="toggler-2" name="toggler-1" type="checkbox" value="2">
                            <label for="toggler-2">
                                <svg class="toggler-on" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                    <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5"></polyline>
                                </svg>
                                <svg class="toggler-off" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                    <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                                    <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
                                </svg>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade bs-example-modal-sm" id="Nueva_tumba_metodo_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nueva Tumba</h4>
            </div>
            <form id="Nueva_tumba_form">
                <div class="modal-body">
                    <div class="form-grup">
                        <label for="">Buscar Titular</label>
                        <div class="form">
                            <label for="search">
                                <input required="" autocomplete="off" placeholder="Buscar Titular" id="Buscador_finado" type="text">
                                <div class="icon_buscador">
                                    <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                    </svg>
                                </div>
                            </label>
                        </div>
                    </div>
                    <hr>
                    <input type="hidden" id="id_finado">

                    <div class="form-grup">
                        <label for="">Seleccione Panteón</label>
                        <select name="" id="selectPanteon" class="form-control" required onchange="cargarLotes()">
                            <option value="" required onchange="cargarLotes()">Panteónes</option>
                            <?php
                            $panteon_editar = mysqli_query($mysqli, "SELECT *FROM mxpt_panteones");
                            while ($da = mysqli_fetch_array($panteon_editar)) { ?>
                                <option name=" <?php echo "" . $da['id_panteon']; ?> " value=" <?php echo $da['id_panteon'] ?>">
                                    <?php echo $da['Panteon'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Seleccione Lote</label>
                        <select name="" id="selectLote" class="form-control" required>

                        </select>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Tipo Tumba</label>
                        <select id="Tipo_Tumba_Metodo_1" class="form-control" required>
                            <option value="">Seleccione tipo Tumba</option>
                            <?php
                            $tipo = mysqli_query($mysqli, "SELECT * FROM catalogo_tipo_tumba");
                            while ($da = mysqli_fetch_array($tipo)) { ?>
                                <option name=" <?php echo "" . $da['id_tipo_tumba']; ?> " value=" <?php echo $da['id_tipo_tumba'] ?>">
                                    <?php echo $da['Tipo_Tumba'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Ingrese Medidas</label>
                        <input type="text" class="form-control" required id="Medidas_Nueva_tumba_1">
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Fecha Inhumación</label>
                        <input type="date" class="form-control" required id="Fecha_Inhumacion_1">
                    </div>
                    <div class="alert alert-verde" id="tumba_bien">

                        <strong>¡Genial!</strong> Su registro se actualizo exitosamente
                    </div>

                    <div class="alert alert-warning" id="tumba_existe">
                        <strong>¡Genial!</strong> Su registro se actualizo exitosamente
                    </div>
                    <div class="alert alert-danger" id="tumba_error">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
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


<div class="modal fade bs-example-modal-sm" id="Nueva_tumba_metodo_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tumba Existente </h4>
            </div>
            <div class="modal-body">
                <div class="form-grup">
                    <label for="">Ingresa Fecha creacion</label>
                    <input type="date" class="form-control">
                </div><br>
                <div class="form-grup">
                    <label for="">Su Periodo de perpetuidad termino</label>
                    <input type="text" class="form-control">
                </div><br>
                <div class="form-grup">
                    <label for="">Ingresa Fecha creacion</label>
                    <input type="text" class="form-control">
                </div><br>
                <div class="form-grup">
                    <label for="">Ingresa Fecha creacion</label>
                    <input type="text" class="form-control">
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade bs-example-modal-sm" id="Editar_tumba_metodo_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nueva Tumba</h4>
            </div>
            <form id="asd">
                <div class="modal-body">
                    <div class="form-grup">
                        <label for="">Buscar Titular</label>
                        <div class="form">
                            <label for="search">
                                <input required="" autocomplete="off" placeholder="Buscar Titular" id="Buscador_finado" type="text">
                                <div class="icon_buscador">
                                    <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                    </svg>
                                </div>
                            </label>
                        </div>
                    </div>
                    <hr>
                    <input type="hidden" id="id_finado">

                    <div class="form-grup">
                        <label for="">Seleccione Panteón</label>
                        <select name="" id="selectPanteon" class="form-control" required onchange="cargarLotes()">
                            <option value="" required onchange="cargarLotes()">Panteónes</option>
                            <?php
                            $panteon_editar = mysqli_query($mysqli, "SELECT *FROM mxpt_panteones");
                            while ($da = mysqli_fetch_array($panteon_editar)) { ?>
                                <option name=" <?php echo "" . $da['id_panteon']; ?> " value=" <?php echo $da['id_panteon'] ?>">
                                    <?php echo $da['Panteon'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Seleccione Lote</label>
                        <select name="" id="selectLote" class="form-control" required>

                        </select>
                    </div>
                    <hr>
                    <div class="form-grup">
                        <label for="">Tipo Tumba</label>
                        <select id="Tipo_Tumba_Metodo_1" class="form-control" required>
                            <option value="">Seleccione tipo Tumba</option>
                            <?php
                            $tipo = mysqli_query($mysqli, "SELECT * FROM catalogo_tipo_tumba");
                            while ($da = mysqli_fetch_array($tipo)) { ?>
                                <option name=" <?php echo "" . $da['id_tipo_tumba']; ?> " value=" <?php echo $da['id_tipo_tumba'] ?>">
                                    <?php echo $da['Tipo_Tumba'] ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <hr>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    // Capturar clic en el botón "Editar"
    $('.recuperar_datos_editar_tumba').on('click', function() {
        var id_tumba = $(this).data('id_tumba');
        var nr_tumba = $(this).data('nr_tumba');
        var nombre_titular = $(this).data('nombre_titular');
        var nombre_finado = $(this).data('nombre_finado');
        var panteon = $(this).data('panteon');
        var tipo_tumba = $(this).data('tipo_tumba');
        var lote = $(this).data('lote');

        // Ahora puedes hacer lo que quieras con estos datos, por ejemplo, mostrarlos en un modal
        $('#modal-id_tumba').text(id_tumba);
        $('#modal-nr_tumba').text(nr_tumba);
        $('#modal-nombre_titular').text(nombre_titular);
        $('#modal-nombre_finado').text(nombre_finado);
        $('#modal-panteon').text(panteon);
        $('#modal-tipo_tumba').text(tipo_tumba);
        $('#modal-lote').text(lote);
    });


    $('#tumba_error').hide();
    $('#tumba_existe').hide();
    $('#tumba_bien').hide();


    $('#Nueva_tumba_metodo_1').on('hidden.bs.modal', function() {
        // Oculta las alertas
        $('#tumba_existe').hide();
        $('#tumba_error').hide();
        $('#tumba_bien').hide();
    });
    $('#Opciones_nueva_tumba').on('hidden.bs.modal', function() {
        $('#toggler-1').prop('checked', false);
        $('#toggler-2').prop('checked', false);
    });


    $(document).ready(function() {
        $('#Nueva_tumba_form').on('submit', function(event) {
            event.preventDefault(); // evita que el formulario se envíe de forma predeterminada


            var id_finado = $('#id_finado').val();
            var selectPanteon = $('#selectPanteon').val();
            var selectLote = $('#selectLote').val();
            var Tipo_Tumba_Metodo_1 = $('#Tipo_Tumba_Metodo_1').val();
            var Medidas_Nueva_tumba_1 = $('#Medidas_Nueva_tumba_1').val();
            var Fecha_Inhumacion_1 = $('#Fecha_Inhumacion_1').val();

            // Creación del objeto formData
            var formData = {
                id_finado: id_finado,
                selectPanteon: selectPanteon,
                selectLote: selectLote,
                Tipo_Tumba_Metodo_1: Tipo_Tumba_Metodo_1,
                Medidas_Nueva_tumba_1: Medidas_Nueva_tumba_1,
                Fecha_Inhumacion_1: Fecha_Inhumacion_1
            };
            // alert(JSON.stringify(formData));

            $.ajax({
                url: 'Altas/Nueva_tumba.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Maneja la respuesta del servidor después de enviar los datos del formulario
                    console.log(response);
                    $('.alert').hide();
                    console.log(response);
                    if (response == 'exito') {
                        $('#tumba_bien').hide().addClass('alert').show();
                        $('#Nueva_tumba_form')[0].reset();
                        $('#tbl-contact').DataTable().ajax.reload();

                    } else if (response == 'existe') {
                        $('#tumba_existe').hide().addClass('alert').show();

                    } else {
                        $('#tumba_error').hide().addClass('alert').show();

                    }
                },
                error: function(xhr, status, error) {
                    // Maneja el error de la solicitud AJAX
                    console.log(xhr.responseText);
                }
            });

        });
    });



    function cargarLotes() {
        var selectPanteon = document.getElementById("selectPanteon");
        var selectLote = document.getElementById("selectLote");

        // Si se selecciona "Panteónes", resetea el segundo select
        if (selectPanteon.value === "") {
            selectLote.innerHTML = ""; // Vacía el contenido del segundo select
            var defaultOption = document.createElement("option");
            defaultOption.text = ''; // O el texto que desees por defecto
            selectLote.add(defaultOption);
        } else {
            var idPanteon = $('#selectPanteon').val();
            $.ajax({
                url: 'Editar/getLotes.php',
                type: 'POST',
                data: {
                    idPanteon: idPanteon
                },
                dataType: 'json',
                success: function(data) {
                    $('#selectLote').html(data);
                }
            });
        }
    }




    $(document).ready(function() {
        $('#toggler-1').on('change', function() {
            if ($(this).prop('checked')) {
                // Mostrar modal 2 después de 1 segundo
                setTimeout(function() {
                    $('#Opciones_nueva_tumba').modal('hide');
                }, 1100);
                setTimeout(function() {
                    $('#Nueva_tumba_metodo_1').modal('show');
                }, 1200);

            } else {
                // Ocultar modal 2 y mostrar modal 1
                $('#Nueva_tumba_metodo_1').modal('hide');
                $('#Opciones_nueva_tumba').modal('show');
            }
        });
    });
    $(document).ready(function() {
        $('#toggler-2').on('change', function() {
            if ($(this).prop('checked')) {
                // Mostrar modal 2 después de 1 segundo
                setTimeout(function() {
                    $('#Opciones_nueva_tumba').modal('hide');
                }, 1100);
                setTimeout(function() {
                    $('#Nueva_tumba_metodo_2').modal('show');
                }, 1200);

            } else {
                // Ocultar modal 2 y mostrar modal 1
                $('#Nueva_tumba_metodo_2').modal('hide');
                $('#Opciones_nueva_tumba').modal('show');
            }
        });
    });

    $(document).ready(function() {
        var totalColumns = $('#tbl-contact thead th').length;

        $('#tbl-contact thead th').each(function(index) {
            var title = $(this).text();
            if (index < totalColumns - 0) {
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
            "ajax": {
                "url": ".././json/server_tumbas.php",
                "error": function(xhr, error, thrown) {
                    console.log('XHR: ', xhr);
                    console.log('Error: ', error);
                    console.log('Thrown error: ', thrown);
                }
            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
                "data": null,
                "render": function(data, type, row) {
                    return '<button class="none"><img src="../img/mas.png" alt="mas" class="expand-icon"></button> ' +
                        row[0];
                }
            }, ],
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

                var Responsable = rowData[11];
                var Boton_Editar = rowData[13];

                row.child('<div style="display: flex;"><span class="fecha-info">Responsable Captura:' +
                    Responsable +
                    '</span><span class="fecha-info">Responsable Captura:' +
                    Boton_Editar +
                    '</span></div>').show();

                tr.addClass('shown');
                $(this).find('.expand-icon').attr('src', '../img/menos.png');
            }
        });

        table.columns().every(function(index) {
            if (index < totalColumns - 0) {
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
        $("#Buscador_finado").autocomplete({
            source: "../json/server_recuperar_datos_finado.php",
            minLength: 1,
            select: function(event, ui) {
                event.preventDefault();
                $('#id_finado').val(ui.item.id_finado);
            },
            // Personalizar la presentación de los resultados
            open: function() {
                $('.ui-autocomplete').css('width', '300px'); // Ajusta el ancho si es necesario
            },
            _renderItem: function(ul, item) {
                return $("<li>")
                    .append("<div><strong>" + item.value.split("\n")[0] + "</strong><br><small>" + item
                        .value.split("\n")[1] + "</small></div>")
                    .appendTo(ul);
            }
        });
    });
</script>