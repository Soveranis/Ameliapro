<?php
include("../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_sistemas'];
if (!isset($_SESSION['user_login_sistemas']) || empty($_SESSION['user_login_sistemas'])) {
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

// Función para contar registros de eventos por fecha
function countEventLogsByDate($logDirectory)
{
    $logCounts = [];

    // Escanea los archivos en el directorio de logs
    $files = scandir($logDirectory);

    foreach ($files as $file) {
        if (preg_match("/log_(\d{4}-\d{2}-\d{2})\.txt$/", $file, $matches)) {
            $date = $matches[1];
            // Abre el archivo
            $filePath = $logDirectory . $file;
            $fileContent = file($filePath);

            // Cuenta las líneas del archivo (registros de eventos)
            $eventCount = count($fileContent);

            // Almacena el conteo en el array
            $logCounts[$date] = $eventCount;
        }
    }

    return $logCounts;
}

// Directorio de logs
$logDirectory = "../event_logs/Logs/";

// Obtiene el conteo de eventos por fecha
$logsData = countEventLogsByDate($logDirectory);

// Codifica los datos en JSON para su uso en JavaScript
$logsDataJSON = json_encode($logsData);
$query = "SELECT u.Usuario, COUNT(*) AS CantidadActividades
FROM mxpt_usuarios u
LEFT JOIN mxpt_titulares a ON u.id_usuario = a.Responsable_cap
GROUP BY u.Usuario
ORDER BY CantidadActividades DESC";
$result = $mysqli->query($query);

$usuarios = [];
$actividades = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row['Usuario'];
    $actividades[] = $row['CantidadActividades'];
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
<style>
    .panel .panel-body {
        padding-top: 10px;
        padding-bottom: 15px;
        height: 600px;
        overflow: overlay;
    }

    .dt-buttons {
        margin: 30px;
    }
</style>



<body>
    <div id="wrapper">

        <!-- Agregamos el menu lateral con los links respectivos -->
        <?php include('../Vistas/Menu_Lateral_user_sistem.php'); ?>

        <div class="main">
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Segundo contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="segundo-contenedor" class="ct-chart">
                                        <h3>Gráfico de Logs</h3>
                                        <canvas id="logsPieChart" width="400px" height="150px"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body" style="height: auto;">
                                    <div id="primer-contenedor" class="ct-chart">
                                        <h3>Registro Event Logs</h3><br>
                                        <form method="post" action="" style="display:flex;">
                                            <div class="form-group">
                                                <label for=""></label>
                                                <select class="selectpicker" name="log_date" data-show-subtext="true" data-live-search="true">
                                                    <option selected>Seleccione un día</option>
                                                    <?php
                                                    $dir = "../event_logs/Logs/";
                                                    $files = scandir($dir);
                                                    $dates = array(); // Array para almacenar fechas

                                                    foreach ($files as $file) {
                                                        if (preg_match("/log_(\d{4}-\d{2}-\d{2})\.txt$/", $file, $matches)) {
                                                            $dates[] = $matches[1]; // Agregar fecha al array
                                                        }
                                                    }

                                                    rsort($dates); // Ordenar fechas en orden descendente

                                                    foreach ($dates as $date) {
                                                        echo "<option value='$date'>$date</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-default" style="height: 35px;margin-left: 22px;">Mostrar</button>
                                            <button type="button" class="btn btn-primary" style="height: 35px;margin-left: 22px;" data-toggle="modal" data-target="#Depuracion">Configurar Depuración</button>
                                        </form>


                                        <?php

                                        date_default_timezone_set('America/Mexico_City');
                                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["log_date"])) {
                                            $selected_date = $_POST["log_date"];
                                            $file_path = $dir . "log_" . $selected_date . ".txt";

                                            if (file_exists($file_path)) {
                                                $file_content = file($file_path);
                                                echo "<table class='table table-bordered' id='myTable'>";
                                                echo "<thead><tr><th>Contenido del Log</th></tr></thead>";
                                                echo "<tbody>";
                                                foreach ($file_content as $line) {
                                                    echo "<tr>";
                                                    echo "<td>" . $line . "</td>";
                                                    echo "</tr>";
                                                }
                                                echo "</tbody>";
                                                echo "</table>";
                                            } else {
                                                echo "<p>No se encontró el archivo de registro para la fecha seleccionada.</p>";
                                            }
                                        } else {
                                            // Lógica para obtener la fecha actual o la fecha seleccionada por el usuario
                                            date_default_timezone_set('America/Mexico_City');
                                            $dir = "../event_logs/Logs/";

                                            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["log_date"])) {
                                                $selected_date = $_POST["log_date"];
                                            } else {
                                                $current_date = date("Y-m-d"); // Obtén la fecha actual en el formato esperado
                                                $selected_date = $current_date; // Asigna la fecha actual como fecha seleccionada por defecto
                                            }

                                            $file_path = $dir . "log_" . $selected_date . ".txt";

                                            if (file_exists($file_path)) {
                                                $file_content = file($file_path);
                                                echo "<table class='table table-bordered' id='myTable'>";
                                                echo "<thead><tr><th>Contenido del Log</th></tr></thead>";
                                                echo "<tbody>";
                                                foreach ($file_content as $line) {
                                                    echo "<tr>";
                                                    echo "<td>" . $line . "</td>";
                                                    echo "</tr>";
                                                }
                                                echo "</tbody>";
                                                echo "</table>";
                                            } else {
                                                echo "<p>No se encontró el archivo de registro para la fecha seleccionada.</p>";
                                            }
                                        } ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Incluimos el Menu celular -->
        <?php include('../Vistas/Menu_celular_user_sistem.php'); ?>
        <!-- Incluimos el footer -->
        <?php include('../Vistas/Footer.php'); ?>
    </div>


</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<div class="modal fade" id="Depuracion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Depuración de Archivos Log</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="display: block !important;">
                    <?php
                    // Consultar el valor actual de la base de datos
                    $query_dupurar = "SELECT Dias_Depuracion_Log, Fecha_Depuracion FROM catalogo_configuracion";
                    $result = $mysqli->query($query_dupurar);
                    $row = $result->fetch_assoc();
                    $valorActual = $row['Dias_Depuracion_Log'];
                    $fechaDepuracion = $row['Fecha_Depuracion'];
                    ?>
                    <p>Seleccione cada cuántos días el sistema borrará los archivos de registro teniendo en cuenta que
                        estos pueden causar a largo plazo estragos en la velocidad del sistema. El valor actual es de
                        <?php echo $valorActual; ?> días.</p>
                    <p>Los archivos se borrarán el <?php echo $fechaDepuracion; ?>.</p>
                </div>
                <form id="Form_Depu">
                    <select name="Nr_Dias" id="Nr_Dias" class="form-control" required>
                        <option value="" disabled selected>Seleccionar cantidad de dias</option>
                        <option value="30">30 Días</option>
                        <option value="40">40 Días</option>
                        <option value="50">50 Días</option>
                        <option value="60">60 Días</option>
                        <option value="80">80 Días</option>
                        <option value="100">100 Días</option>
                    </select>



            </div>
            <div class="alert alert-verde" style="display:none;" id="mensaje-exito">
                <strong>¡Éxito!</strong> ¡Se ha actualizado su usuario correctamente!
            </div>

            <div class="alert alert-danger" style="display:none;" id="mensaje-error">
                <strong>¡Error!</strong> Algo salió mal, intente más tarde.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-primary">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="cambiar_contra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Cambiar Contraseña</h4>
            </div>
            <div class="modal-body">
                <form id="formulario_cambio_contra">
                    <div class="alert alert-info" id="formulario__mensaje-exito-3">
                        <strong>¡Aviso!</strong> Un administrador ha restablecido temporalmente tu cuenta por motivos de
                        confidencialidad. Por favor, procede a cambiar tu contraseña de forma inmediata
                    </div>
                    <input type="hidden" name="id_usuario_editar" id="id_usuario_editar_3">
                    <!-- Grupo: Contraseña -->
                    <div class="formulario__grupo" id="grupo__contras_1">
                        <label for="pass1" class="formulario__label">Contraseña</label>
                        <div class="formulario__grupo-input">
                            <input type="text" class="formulario__input" name="contras_1" id="contras_1">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">La contraseña debe tener entre 8 y 10 caracteres, incluyendo al menos una
                            letra mayúscula, una minúscula, un número y un símbolo seguro.</p>
                    </div>

                    <div id="ocultarcontraseña2">
                        <!-- Grupo: Contraseña 2 -->
                        <div class="formulario__grupo" id="grupo__pass2">
                            <label for="pass2" class="formulario__label">Repetir Contraseña</label>
                            <div class="formulario__grupo-input">
                                <input type="text" required class="formulario__input" name="pass2" id="pass2">
                                <i class="formulario__validacion-estado fas fa-times-circle"></i>
                            </div>
                            <p class="formulario__input-error">Ambas contraseñas deben ser iguales.</p>
                        </div>
                    </div>


                    <div class="alert alert-verde" style="display:none;" id="formulario__mensaje-exito-3">
                        <strong>¡Éxito!</strong> ¡Se ha agregado su usuario correctamente!
                    </div>
                    <div class="alert alert-warning" style="display:none;" id="Rellene_campos-3">
                        <strong>¡Error!</strong> Por favor rellene los campos correctamente.
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="formulario__mensaje-error-3">
                        <strong>¡Error!</strong> Algo salió mal, intente más tarde.
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    function Activar_modal() {
        $('#cambiar_contra').modal('show');
    }

    const formulario_3 = document.getElementById('formulario_cambio_contra');
    const inputs_3 = document.querySelectorAll('#formulario_cambio_contra input');
    const expresiones_3 = {
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,10}$/, // letras mayúsculas, minúsculas, números y símbolos seguros, de 8 a 10 caracteres.
    };

    const campos_3 = {
        contras_1: false,
        pass2: false,
    };

    const validarFormulario_3 = (e) => {
        switch (e.target.name) {
            case "contras_1":
                validarCampo_3(expresiones_3.password, e.target, 'contras_1');
                validarPassword2();
                break;
            case "pass2":
                validarPassword2();
                break;
        }
    };

    const validarCampo_3 = (expresion, input, campo) => {
        if (expresion.test(input.value)) {
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-check-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-times-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.remove(
                'formulario__input-error-activo');
            campos_3[campo] = true;

        } else {
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos_3[campo] = false;

        }
    };

    const validarPassword2 = () => {
        const inputPassword1 = document.getElementById('contras_1');
        const inputPassword2 = document.getElementById('pass2');

        if (inputPassword1.value === '' && inputPassword2.value === '') {
            // Ambos campos están vacíos
            document.getElementById(`grupo__contras_1`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__contras_1`).classList.remove('formulario__grupo-correcto');
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__contras_1 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__contras_1 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__contras_1 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos_3['contras_1'] = false;

            return;
        }

        if (inputPassword1.value !== inputPassword2.value) {
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos_3['pass2'] = false;

        } else {
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-correcto');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-check-circle');

            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.remove(
                'formulario__input-error-activo');
            campos_3['pass2'] = true;

        }
    };

    inputs_3.forEach((input) => {
        input.addEventListener('keyup', validarFormulario_3);
        input.addEventListener('blur', validarFormulario_3);
    });



    $(document).ready(function() {
        $('#formulario_cambio_contra').on('submit', function(e) {
            e.preventDefault();
            $('.alert').hide();

            if (campos_3.contras_1 && campos_3.pass2) {
                var formData = $('#formulario_cambio_contra').serializeArray();
                formData.push({
                    name: 'id_usuario',
                    value: $('#id_usuario_editar_3').val()
                });
                // alert(JSON.stringify(formData));
                $.ajax({
                    url: 'Editar/Editar_contra_user.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('.alert').hide();
                        console.log(response);
                        if (response == 'Exito') {
                            $('#formulario__mensaje-exito-3').hide().addClass('alert').show();
                            $('#cambiar_contra').modal(
                                'hide'); // Oculta el modal "cambiar_contra"
                        } else {
                            $('#formulario__mensaje-error-2').hide().addClass('alert').show();
                        }

                    },
                    error: function(xhr, status, error) {
                        console.log('Error en la solicitud AJAX:', error);
                        console.log('Respuesta completa:', xhr.responseText);
                    }
                });
            } else {
                $('#Rellene_campos-3').hide().addClass('alert').show();
            }
        });
    });
    $('.modal').on('hidden.bs.modal', function() {
        // Oculta las alertas
        $('#mensaje-exito').hide();
        $('#mensaje-error').hide();
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
    $(document).on('submit', '#Form_Depu', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var Nr_Dias = $('#Nr_Dias').val();

        // Crea un objeto con los datos del formulario
        var formData = {
            Nr_Dias: Nr_Dias,
        };

        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Editar/Editar_Dias.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                if (response == 'Exito') {
                    $('#mensaje-error').hide(); // Oculta el mensaje de error
                    $('#mensaje-exito').addClass('alert').show(); // Muestra el mensaje de éxito
                } else {
                    $('#mensaje-exito').hide(); // Oculta el mensaje de éxito
                    $('#mensaje-error').addClass('alert').show(); // Muestra el mensaje de error
                }
            },
            error: function(xhr, status, error) {
                // Maneja el error de la solicitud AJAX
                console.log(xhr.responseText);
            }
        });
    });

    $(document).ready(
        function() { //creamos una función usando datatables para poder hacer un crud este crud es diferente que los demás mas fácil pero tiene la desventaja que si lo hacemos con muchos registros se va a trabar este es perfecto para tablas que no superen los 2000 registros
            $('#myTable').DataTable({

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


                },
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'pdf',
                }],

            });

        });


// Obtener los datos de los logs desde PHP
var logsData = <?php echo $logsDataJSON; ?>;

// Obtener las fechas y cantidades de logs como arreglos separados
var logDates = Object.keys(logsData);
var logCounts = Object.values(logsData);

// Crear el gráfico de barras
var ctx = document.getElementById('logsPieChart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: logDates,
        datasets: [{
            label: 'Cantidad de Logs',
            data: logCounts,
            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo de las barras
            borderColor: 'rgba(75, 192, 192, 1)', // Color del borde de las barras
            borderWidth: 1 // Ancho del borde de las barras
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Función para cambiar el tipo de gráfico en función del tamaño de la pantalla
function actualizarTipoDeGrafico() {
    // Obtener el ancho de la ventana
    var anchoVentana = window.innerWidth;

    // Cambiar el tipo de gráfico según el ancho de la ventana
    if (anchoVentana <= 500) {
        chart.config.type = 'pie'; // Cambiar a gráfico de pastel
    } else {
        chart.config.type = 'bar'; // Cambiar a gráfico de barras
    }

    // Actualizar el gráfico
    chart.update();
}

// Asignar la función al evento resize
window.addEventListener('resize', actualizarTipoDeGrafico);

// Llamar a la función inicialmente para establecer el tipo de gráfico correcto
actualizarTipoDeGrafico();


    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>