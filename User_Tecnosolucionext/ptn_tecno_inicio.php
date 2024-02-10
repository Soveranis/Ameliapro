<?php
include("../conexiones/conexion.php");
session_start();
$username = $_SESSION['user_login_tecno'];
if (!isset($_SESSION['user_login_tecno']) || empty($_SESSION['user_login_tecno'])) {
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
        <?php include('../Vistas/Menu_Lateral_user_tecno.php'); ?>


        <div class="main">
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Segundo contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <h3>Administrador de Módulos</h3>
                                    <form id="Guardar_Cambios">
                                        <button class="btn btn-primary">Guardar Cambios</button><br>
                                        
                                        <div class="alert alert-verde" style="display:none;" id="Exito">
                                            <strong>¡Aviso!</strong> Se actualizaron los datos correctamente
                                        </div><br>

                                        <div class="alert alert-danger" style="display:none;" id="Error">
                                            <strong>¡Error!</strong> Parece que algo salió mal.
                                            Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                                            - Verifica tu conexión a internet.
                                            - Actualiza la página o la aplicación.
                                            - Cierra sesión y vuelve a iniciar sesión.
                                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.

                                        </div><br>
                                        <div class="table-responsive">
                                            <table id="myTable" class="table-striped table-bordered table-sm table-responsive-sm" style="width:100%;">
                                                <caption class="caption table-bordered table-striped">
                                                    Módulos</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Tumbas</th>
                                                        <th>Pagos</th>
                                                        <th>Reporte General</th>
                                                        <th>Servicios</th>
                                                        <th>Panteones</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $sqsl = $mysqli->query("SELECT * FROM catalogo_modulos");
                                                    while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                                                        <tr>
                                                            <td><?php echo $row['id_modulo']; ?></td>
                                                            <td>
                                                                <select class="form-control" id="Tumbas">
                                                                    <option value="1" <?php if ($row['Tumbas'] == 1) echo "selected"; ?>>Activo</option>
                                                                    <option value="0" <?php if ($row['Tumbas'] == 0) echo "selected"; ?>>Inactivo</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="Pagos">
                                                                    <option value="1" <?php if ($row['Pagos'] == 1) echo "selected"; ?>>Activo</option>
                                                                    <option value="0" <?php if ($row['Pagos'] == 0) echo "selected"; ?>>Inactivo</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="Reportes_Generales">
                                                                    <option value="1" <?php if ($row['Reporte_General'] == 1) echo "selected"; ?>>Activo</option>
                                                                    <option value="0" <?php if ($row['Reporte_General'] == 0) echo "selected"; ?>>Inactivo</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="Servicios">
                                                                    <option value="1" <?php if ($row['Servicios'] == 1) echo "selected"; ?>>Activo</option>
                                                                    <option value="0" <?php if ($row['Servicios'] == 0) echo "selected"; ?>>Inactivo</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="Panteones">
                                                                    <option value="1" <?php if ($row['Panteones'] == 1) echo "selected"; ?>>Activo</option>
                                                                    <option value="0" <?php if ($row['Panteones'] == 0) echo "selected"; ?>>Inactivo</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
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
    $(document).on('submit', '#Guardar_Cambios', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        // Obtén los datos del formulario
        var estado_modulo_Titulares = $('#Titulares').val();
        var estado_modulo_Finados = $('#Finados').val();
        var estado_modulo_Tumbas = $('#Tumbas').val();
        var estado_modulo_Pagos = $('#Pagos').val();
        var estado_modulo_Reporte_Servicios = $('#Servicios').val();
        var Reportes_Generales = $('#Reportes_Generales').val();
        var estado_modulo_Panteones = $('#Panteones').val();

        // Crea un objeto con los datos del formulario
        var formData = {
            estado_modulo_Titulares: estado_modulo_Titulares,
            estado_modulo_Finados: estado_modulo_Finados,
            estado_modulo_Tumbas: estado_modulo_Tumbas,
            estado_modulo_Pagos: estado_modulo_Pagos,
            Reportes_Generales: Reportes_Generales,
            estado_modulo_Reporte_Servicios: estado_modulo_Reporte_Servicios,
            estado_modulo_Panteones: estado_modulo_Panteones
        };


        // alert(JSON.stringify(formData));
        // Envía los datos del formulario a través de AJAX
        $.ajax({
            url: 'Editar/Actualizar_modulos.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);

                $('.alert').hide();
                console.log(response);
                if (response == 'exito') {
                    $('#Exito').hide().addClass('alert').show();
                    setTimeout(function() {
                        $('#Exito').hide(); // Oculta el mensaje de éxito después de 5 segundos
                    }, 5000);
                } else {
                    $('#Error').hide().addClass('alert').show();
                    setTimeout(function() {
                        $('#Error').hide(); // Oculta el mensaje de error después de 5 segundos
                    }, 5000);
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
                "autoFill": true,
                "ordering": false,
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
                    "search": false,
                    "zeroRecords": "No se encontraron registros ",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },


                }

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