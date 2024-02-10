<?php
include("../conexiones/conexion.php");

session_start();
$username = $_SESSION['user_login_sistemas'];
if (!isset($_SESSION['user_login_sistemas']) || empty($_SESSION['user_login_sistemas'])) {
    header('Location: ../');
    exit(); // Asegúrate de terminar la ejecución aquí
}

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

$query = "SELECT Logo_Principal, Logo_Secundario , favicoin , Leyenda_Departamento FROM catalogo_configuracion";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($logoPrincipal_blob, $logoSecundario_blob, $favicoin_blob, $leyenda);
$stmt->fetch();

$logoPrincipal_data = base64_encode($logoPrincipal_blob);
$logoSecundario_data = base64_encode($logoSecundario_blob);
$favicoint_data = base64_encode($favicoin_blob);
?>
<!doctype html>
<html lang="en">

<head>
    <title>Departamento Panteones</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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
</head>

<style>
    .panel .panel-body {

        overflow: overlay;
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


                        <div class="col-md-4">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">
                                            <div class="btn-edit-admin">
                                                <i class="fa-solid fa-pen-to-square" data-toggle="modal" data-target="#my_principal"></i>
                                            </div>
                                            <center>
                                                <h3>Logo principal</h3>
                                            </center>
                                            <hr>
                                            <div class="cont-centrar_contenido_inicio">

                                                <div class="cont-centrar_contenido_inicio">
                                                    <?php
                                                    if (isset($logoPrincipal_blob) && !empty($logoPrincipal_blob)) {
                                                        $base64Image = base64_encode($logoPrincipal_blob);
                                                    } else {
                                                        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
                                                        $base64Image = base64_encode(file_get_contents('../img/Logos Municipios/Logo_Principal_defecto.png'));
                                                    }
                                                    ?>

                                                    <img src="data:image/jpeg;base64,<?php echo $base64Image; ?>" alt="Defecto" class="fondo_defecto_logos principal">
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">
                                            <div class="btn-edit-admin">
                                                <i class="fa-solid fa-pen-to-square" data-toggle="modal" data-target="#my_horizontal"></i>
                                            </div>
                                            <center>
                                                <h3>Logo Horizontal</h3>
                                            </center>
                                            <hr>
                                            <?php
                                            if (isset($logoSecundario_blob) && !empty($logoSecundario_blob)) {
                                                $base64Image = base64_encode($logoSecundario_blob);
                                            } else {
                                                // Si $logoSecundario_blob no está definido o está vacío, usa una URL por defecto
                                                $base64Image = base64_encode(file_get_contents('../img/Logos Municipios/Logo_Estirado_Defecto.png'));
                                            }
                                            ?>

                                            <div class="cont-centrar_contenido_inicio">
                                                <img src="data:image/jpeg;base64,<?php echo $base64Image; ?>" alt="Defecto" class="fondo_defecto_logos">
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">
                                            <div class="btn-edit-admin">
                                                <i class="fa-solid fa-pen-to-square" data-toggle="modal" data-target="#my_favicoin"></i>
                                            </div>
                                            <center>
                                                <h3>Favicon</h3>
                                            </center>
                                            <hr>

                                            <?php
                                            if (isset($favicoin_blob) && !empty($favicoin_blob)) {
                                                $base64Image = base64_encode($favicoin_blob);
                                            } else {
                                                // Si $favicoin_blob no está definido o está vacío, usa una URL por defecto
                                                $base64Image = base64_encode(file_get_contents('../img/Logos Municipios/Favicoin_defecto.png'));
                                            }
                                            ?>

                                            <div class="cont-centrar_contenido_inicio">
                                                <img src="data:image/jpeg;base64,<?php echo $base64Image; ?>" alt="Defecto" class="fondo_defecto_favicoin">
                                            </div>

                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">

                                            <center>
                                                <h3>Fondo del sistema</h3>
                                            </center>
                                            <hr>

                                            <form id="form-fondo_pantalla">
                                                <div class="alert alert-info">
                                                    Por favor, elige una imagen en formato JPEG o PNG con buena calidad
                                                    de imagen no
                                                    mayor de 3MB y con medidas de 1920x1080 .
                                                </div>
                                                <div class="cont-centrar_contenido_inicio">
                                                    <input type="file" class="form-control" name="input_fondo_pantalla" id="input_fondo_pantalla" accept="image/jpeg, image/png" required>
                                                </div>
                                                <div class="alert alert-verde" style="display:none;" id="fondo_pantalla_bien">
                                                    <center>
                                                        <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente el
                                                        fondo
                                                        predeterminado
                                                        <button class="btn btn" onclick="location.reload();">Actualizar</button> para ver
                                                        cambios
                                                    </center>
                                                </div>
                                                <div class="alert alert-danger" style="display:none;" id="fondo_pantalla_mal">
                                                    <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                                                    - Verifica tu conexión a internet.
                                                    - Actualiza la página o la aplicación.
                                                    - Cierra sesión y vuelve a iniciar sesión.
                                                    Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                                                </div>
                                                <div class="alert alert-danger" style="display:none;" id="no_cumple_4">
                                                    <strong>¡Error!</strong> Por favor, elige una imagen con dimensiones
                                                    de 1920x1080
                                                </div>
                                                <div class="alert alert-danger" style="display:none;" id="no_cumple_5">
                                                    <strong>¡Error!</strong> Por favor, elige una imagen en formato JPEG
                                                    o PNG con un tamaño no mayor de 3MB.
                                                </div>
                                                <hr>
                                                <button type="submit" class="btn btn-primary" style="float: right;">Guardar</button>


                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">

                                            <center>
                                                <h3>Color Predominante del sistema</h3>
                                            </center>
                                            <hr>

                                            <form id="Form_nuevo_color">
                                                <div class="cont-centrar_contenido_inicio">
                                                    <input type="color" style="width: 60px; height: 60px; padding: 0; margin: auto; border: none; display: block;" id="Nu_color">
                                                </div>
                                                <div class="alert alert-verde" style="display:none;" id="Color_bien">
                                                    <center>
                                                        <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente el
                                                        color
                                                        predeterminado
                                                        <button class="btn btn" onclick="location.reload();">Actualizar</button> para ver
                                                        cambios
                                                    </center>
                                                </div>

                                                <div class="alert alert-danger" style="display:none;" id="Color_mal">
                                                    <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                                                    - Verifica tu conexión a internet.
                                                    - Actualiza la página o la aplicación.
                                                    - Cierra sesión y vuelve a iniciar sesión.
                                                    Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                                                </div>
                                                <hr>
                                                <button class="btn btn-primary" style="float: right;" type="submit">Guardar</button>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">
                                        <div id="demo-area-chart" class="ct-chart">

                                            <center>
                                                <h3>Configuracion General</h3>
                                            </center>
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Leyenda</h4>
                                                    <div class="btn-edit-admin" style="margin-top: -36px;">
                                                        <i class="fa-solid fa-pen-to-square" data-toggle="modal" data-target="#Leyenda"></i>
                                                    </div>
                                                    <hr>
                                                    <!-- Contenido de la primera parte -->
                                                    <p><?php echo $leyenda ?></p>

                                                </div>
                                                <div class="col-md-6">

                                                    <!-- Contenido de la segunda parte -->
                                                    <h4>Ayuntamiento</h4>

                                                    <hr>

                                                    <form id="form_datos_generales">
                                                        <div class="form-grup">
                                                            <label for="">Nombre del Instituto</label>
                                                            <input type="text" class="form-control" id="Nombre_instituto" required>
                                                        </div><br>
                                                        <div class="alert alert-info">
                                                            Limite que tiene para acreditar una orden de
                                                            pago al crearla
                                                        </div>
                                                        <div class="form-grup">
                                                            <label for="">Días hábiles Límite de pago</label>
                                                            <input type="number" class="form-control" id="Fecha_limite_pago" required min="1">

                                                        </div><br>
                                                        <div class="alert alert-danger" style="display: none;" id="instituto_mal">
                                                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                                                            - Verifica tu conexión a internet.
                                                            - Actualiza la página o la aplicación.
                                                            - Cierra sesión y vuelve a iniciar sesión.
                                                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                                                        </div>
                                                        <div class="alert alert-verde" style="display: none;" id="instituto_bien">
                                                            <strong>¡Éxito!</strong> Los datos se actualizaron
                                                            correctamente.
                                                        </div>

                                                        <hr>
                                                        <button class="btn btn-primary" style="float: right;" type="submit">Guardar</button>
                                                    </form>

                                                </div>

                                            </div>

                                            <hr>
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
        <?php include('../Vistas/Menu_celular_user_sistem.php'); ?>
        <!-- Incluimos el footer -->
        <?php include('../Vistas/Footer.php'); ?>
    </div>
</body>

</html>

<div class="modal fade" id="Leyenda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Ingrese la nueva leyenda</h4>
            </div>
            <div class="modal-body">
                <form id="form_nueva_leyenda">

                    <textarea name="Nueva_leyenda" id="Nueva_leyenda" cols="30" rows="10" class="form-control"></textarea>
                    <div class="alert alert-verde" style="display:none;" id="leyenda_bien">
                        <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente su leyenda !
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="leyenda_mal">
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
<!-- Modal -->
<div class="modal fade" id="my_principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Logo Principal </h4>
            </div>
            <div class="modal-body">
                <form id="Formul_Log_1">
                    <div class="form-grup">
                        <div class="alert alert-info">
                            Por favor, elige una imagen en formato JPEG o PNG con un tamaño no mayor de 1MB.
                        </div>
                        <input type="file" class="form-control" id="id_logo_principal" name="Logo" accept="image/jpeg, image/png" required>

                    </div>

                    <div class="alert alert-verde" style="display:none;" id="Logo_principal_bien">
                        <center>
                            <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente su logo principal! <br>
                            <button class="btn btn" onclick="location.reload();">Actualizar</button> para ver
                            cambios
                        </center>

                    </div>
                    <div class="alert alert-danger" style="display:none;" id="Logo_principal_mal">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="No_cumple">
                        <strong>¡Error!</strong> No cumple los requesitos
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
<div class="modal fade" id="my_horizontal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Logo Horitontal </h4>
            </div>
            <div class="modal-body">
                <form id="Formul_Log_2">
                    <div class="form-grup">
                        <div class="alert alert-info">
                            Por favor, elige una imagen en formato JPEG o PNG con un tamaño no mayor de 1MB
                        </div>
                        <input type="file" class="form-control" id="id_logo_horizontal" name="logo_horizontal" accept="image/jpeg, image/png" required>

                    </div>
                    <div class="alert alert-danger" style="display:none;" id="No_cumple_2">
                        <strong>¡Error!</strong> No cumple los requesitos
                    </div>
                    <div class="alert alert-verde" style="display:none;" id="Logo_principal_bien_2">
                        <center>
                            <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente su logo Horitontal! <br>
                            <button class="btn btn" onclick="location.reload();">Actualizar</button> para ver
                            cambios
                        </center>
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="Logo_principal_mal_2">
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
<div class="modal fade" id="my_favicoin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Favicon</h4>
            </div>
            <div class="modal-body">
                <form id="Formul_Log_3">
                    <h3 style="color:#676a6d;">¿Que es un Favicon?</h3>
                    <p>Un "favicon" es un icono de sitio web que aparece en las pestañas del navegador y en la lista de
                        favoritos. Sirve para identificar visualmente un sitio web, brindando a los usuarios una
                        referencia rápida y única del sitio que están visitando. Por lo general, los favicons son
                        versiones reducidas del logotipo o la marca del sitio.</p>
                    <img src="../img/Ejemplo_favicoin.png" class="ejemplo_favicoin">
                    <div class="form-grup">
                        <div class="alert alert-info">
                            Por favor, elige una imagen en formato JPEG o PNG con un tamaño no mayor de 1MB
                        </div>
                        <input type="file" class="form-control" id="form_favi" name="form_favi" accept="image/jpeg, image/png" required>

                    </div>

                    <div class="alert alert-verde" style="display:none;" id="Logo_principal_bien_3">
                        <center>
                            <strong>¡Éxito!</strong> ¡Se ha actualizado correctamente su logo favioin! <br>
                            <button class="btn btn" onclick="location.reload();">Actualizar</button> para ver
                            cambios
                        </center>
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="Logo_principal_mal_3">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="No_cumple_3">
                        <strong>¡Error!</strong> No cumple los requesitos
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $('#form_datos_generales').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // Obtiene los datos del formulario
        var Nombre_instituto = $('#Nombre_instituto').val();
        var Fecha_limite_pago = $('#Fecha_limite_pago').val();


        // Crea un objeto con los datos del formulario
        var formData = {
            Nombre_instituto: Nombre_instituto,
            Fecha_limite_pago: Fecha_limite_pago
        };

        $.ajax({
            url: 'Editar/Editar_instituto.php', // Reemplaza con la URL correcta
            method: 'POST', // Método de envío
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response === 'Exito') {
                    $('#instituto_mal').hide(); // Oculta el mensaje de error
                    $('#instituto_bien').addClass('alert').show(); // Muestra el mensaje de éxito
                } else {
                    $('#instituto_bien').hide(); // Oculta el mensaje de éxito
                    $('#instituto_mal').addClass('alert').show(); // Muestra el mensaje de error
                }
            }
        });
    });
    $(document).on('submit', '#form-fondo_pantalla', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        var predeterminada = $('#input_fondo_pantalla')[0].files[0];

        // Valida el formato, tamaño y dimensiones de la imagen
        if (predeterminada) {
            var allowedFormats = ['image/jpeg', 'image/png'];
            var maxSize = 3024 * 3024; // 1 MB
            var maxWidth = 1920;
            var maxHeight = 1080;

            if (allowedFormats.includes(predeterminada.type) && predeterminada.size <= maxSize) {
                var img = new Image();
                img.src = URL.createObjectURL(predeterminada);

                img.onload = function() {
                    if (img.width === maxWidth && img.height === maxHeight) {
                        // La imagen cumple con los requisitos de formato, tamaño y dimensiones

                        // Crea un objeto FormData para enviar el archivo
                        var formData = new FormData();
                        formData.append('input_fondo_pantalla', predeterminada);

                        // Configura la solicitud fetch
                        fetch('Editar/Editar_fondo.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text())
                            .then(data => {
                                // Maneja la respuesta del servidor después de enviar los datos del formulario
                                console.log(data);
                                if (data === 'Exito') {
                                    $('#no_cumple_4').hide();
                                    $('#no_cumple_5').hide();
                                    $('#fondo_pantalla_mal').hide(); // Oculta el mensaje de error
                                    $('#fondo_pantalla_bien').addClass('alert')
                                        .show(); // Muestra el mensaje de éxito
                                } else {
                                    $('#fondo_pantalla_bien').hide(); // Oculta el mensaje de éxito
                                    $('#no_cumple_4').hide();
                                    $('#no_cumple_5').hide();
                                    $('#fondo_pantalla_mal').addClass('alert')
                                        .show(); // Muestra el mensaje de error
                                }
                            })
                            .catch(error => {
                                // Maneja el error de la solicitud fetch
                                console.log(error);
                            });
                    } else {
                        // No cumple con las dimensiones
                        $('#fondo_pantalla_mal').hide();
                        $('#no_cumple_4').addClass('alert').show(); // Muestra el mensaje de error
                    }
                };
            } else {
                // No cumple con el formato o el tamaño
                $('#fondo_pantalla_mal').hide();
                $('#no_cumple_5').addClass('alert').show(); // Muestra el mensaje de error
            }
        } else {
            // No se ha seleccionado una imagen
            alert('Por favor, selecciona una imagen.');
        }
    });


    $('#form_nueva_leyenda').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // Obtiene los datos del formulario
        var Nueva_leyenda = $('#Nueva_leyenda').val();

        // Crea un objeto con los datos del formulario
        var formData = {
            Nueva_leyenda: Nueva_leyenda,
        };

        $.ajax({
            url: 'Editar/Editar_Leyenda.php', // Reemplaza con la URL correcta
            method: 'POST', // Método de envío
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response === 'Exito') {
                    $('#leyenda_mal').hide(); // Oculta el mensaje de error
                    $('#leyenda_bien').addClass('alert')
                        .show(); // Muestra el mensaje de éxito
                } else {
                    $('#leyenda_bien').hide(); // Oculta el mensaje de éxito
                    $('#leyenda_mal').addClass('alert')
                        .show(); // Muestra el mensaje de error
                }
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        // Obtén el valor de --color-primario del CSS
        var colorPrimario = getComputedStyle(document.documentElement).getPropertyValue('--color-primario');

        // Obtén el elemento de entrada de color
        var inputColor = document.getElementById("Nu_color");

        // Establece el valor del campo de entrada de color
        inputColor.value = colorPrimario;
    });
    $('#Form_nuevo_color').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // Obtiene los datos del formulario
        var colorPicker = $('#Nu_color').val();

        // Crea un objeto con los datos del formulario
        var formData = {
            colorPicker: colorPicker,
        };

        $.ajax({
            url: 'Editar/Editar_color.php', // Reemplaza con la URL correcta
            method: 'POST', // Método de envío
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response === 'Exito') {
                    $('#Color_mal').hide(); // Oculta el mensaje de error
                    $('#Color_bien').addClass('alert').show(); // Muestra el mensaje de éxito
                } else {
                    $('#Color_bien').hide(); // Oculta el mensaje de éxito
                    $('#Color_mal').addClass('alert').show(); // Muestra el mensaje de error
                }
            }
        });
    });

    $(document).ready(function() {
        // Agregar un controlador de eventos para evitar el cierre del modal al hacer clic fuera de él
        $('.modal').on('click', function(event) {
            if ($(event.target).hasClass('modal')) {
                event.stopPropagation(); // Evita que el evento llegue al documento
            }
        });
    });
    $('.modal').on('hidden.bs.modal', function() {
        $('#Logo_principal_mal_3').hide();
        $('#Logo_principal_mal_2').hide();
        $('#Logo_principal_bien_3').hide();
        $('#Logo_principal_bien_2').hide();
        $('#Logo_principal_mal').hide();
        $('#Logo_principal_bien').hide();
    });


    $(document).on('submit', '#Formul_Log_3', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        var Favicon = $('#form_favi')[0].files[0];

        // Valida el formato y el tamaño del archivo
        if (Favicon) {
            var allowedFormats = ['image/jpeg', 'image/png'];
            var maxSize = 3024 * 3024; // 3 MB

            if (allowedFormats.includes(Favicon.type) && Favicon.size <= maxSize) {
                // La imagen cumple con los requisitos de formato y tamaño

                // Crea un objeto FormData para enviar el archivo
                var formData = new FormData();
                formData.append('form_favi', Favicon);

                // Configura la solicitud fetch
                fetch('Editar/Editar_Favicon.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Maneja la respuesta del servidor después de enviar los datos del formulario
                        console.log(data);
                        if (data === 'Exito') {
                            $('#Logo_principal_mal_3').hide();
                            $('#No_cumple_3').hide(); // Oculta el mensaje de error
                            $('#Logo_principal_bien_3').addClass('alert').show(); // Muestra el mensaje de éxito
                        } else {
                            $('#Logo_principal_bien_3').hide(); // Oculta el mensaje de éxito
                            $('#Logo_principal_mal_3').addClass('alert').show(); // Muestra el mensaje de error
                            $('#No_cumple_3').hide();
                        }
                    })
                    .catch(error => {
                        // Maneja el error de la solicitud fetch
                        console.log(error);
                    });
            } else {
                // La imagen no cumple con los requisitos de formato o tamaño
                $('#No_cumple_3').addClass('alert').show();
            }
        } else {
            // No se seleccionó ninguna imagen
            alert('Por favor, selecciona una imagen.');
        }
    });

    $(document).on('submit', '#Formul_Log_2', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        var Horizontal = $('#id_logo_horizontal')[0].files[0];

        // Valida el formato y el tamaño de la imagen
        if (Horizontal) {
            var allowedFormats = ['image/jpeg', 'image/png'];
            var maxSize = 1024 * 1024; // 1 MB

            if (allowedFormats.includes(Horizontal.type) && Horizontal.size <= maxSize) {
                // La imagen cumple con los requisitos de formato y tamaño

                // Crea un objeto FormData para enviar el archivo
                var formData = new FormData();
                formData.append('logo_horizontal', Horizontal);

                // Configura la solicitud fetch
                fetch('Editar/Editar_Logo_Horizontal.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Maneja la respuesta del servidor después de enviar los datos del formulario
                        console.log(data);
                        if (data === 'Exito') {
                            $('#No_cumple_2').hide();
                            $('#Logo_principal_mal_2').hide();
                            $('#Logo_principal_bien_2').addClass('alert').show();
                        } else {
                            $('#Logo_principal_bien_2').hide();
                            $('#No_cumple_2').hide();
                            $('#Logo_principal_mal_2').addClass('alert').show();
                        }
                    })
                    .catch(error => {
                        // Maneja el error de la solicitud fetch
                        console.log(error);
                    });
            } else {
                // La imagen no cumple con los requisitos de formato o tamaño
                $('#No_cumple_2').addClass('alert').show();
            }
        } else {
            // No se seleccionó ninguna imagen
            alert('Por favor, selecciona una imagen.');
        }
    });


    $(document).on('submit', '#Formul_Log_1', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

        var LogoInput = $('#id_logo_principal')[0].files[0];

        // Valida el formato y el tamaño de la imagen
        if (LogoInput) {
            var allowedFormats = ['image/jpeg', 'image/png'];
            var maxSize = 1024 * 1024; // 1 MB

            if (allowedFormats.includes(LogoInput.type) && LogoInput.size <= maxSize) {
                // La imagen cumple con los requisitos

                // Crea un objeto FormData para enviar el archivo
                var formData = new FormData();
                formData.append('Logo', LogoInput);

                // Configura la solicitud fetch
                fetch('Editar/Editar_Logo_principal.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Maneja la respuesta del servidor después de enviar los datos del formulario
                        console.log(data);
                        if (data === 'Exito') {
                            $('#Logo_principal_mal').hide();
                            $('#No_cumple').hide();
                            $('#Logo_principal_bien').addClass('alert').show();
                        } else {
                            $('#Logo_principal_bien').hide();
                            $('#No_cumple').hide();
                            $('#Logo_principal_mal').addClass('alert').show();
                        }
                    })
                    .catch(error => {
                        // Maneja el error de la solicitud fetch
                        console.log(error);
                    });
            } else {
                // La imagen no cumple con los requisito
                $('#No_cumple').addClass('alert').show();
            }
        } else {
            // No se seleccionó ninguna imagen
            alert('Por favor, selecciona una imagen.');
        }
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
    $(document).ready(function() {
        var tablaUsuarios = $('#Usuarios').DataTable({
            "responsive": true,
            "orderFixed": [
                [0, "asc"]
            ],
            "ordering": false,
            "pageLength": 4,
            "language": {
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

        // Agregar botones personalizados
        var addButton =
            '<button class="btn btn-warning" id="agregarButton"  data-toggle="modal" data-target="#Nuevo_usuario" style="margin: 10px 10px -10px 10px;">Agregar Usuario</button>';
        var deleteButton =
            '<button class="btn btn-primary"data-toggle="modal" data-target="#Usuarios_Inactivos" id="eliminarButton" style="margin: 10px 10px -10px 10px;">Usuarios Inactivos</button>';

        // Insertar los botones en el DOM
        $('#Usuarios_wrapper .dataTables_length').after(addButton + deleteButton);
    });
</script>