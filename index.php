<?php
// Incluimos la conexion a la base de datos
include("conexiones/conexion.php");
// Incluimos el repurador de logs 
include("event_logs/Event_Depurar/Eliminar_log.php");
// Query para sacar los logos personalizados desde la base de datos este comando se repite en todos los modulos visibles para el usuario
$query = "SELECT Logo_Principal, Logo_Secundario , favicoin FROM catalogo_configuracion";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($logoPrincipal_blob, $logoSecundario_blob, $favicoin_blob);
$stmt->fetch();
// Extraemos los datos con la funcion "base64_encode"
$logoPrincipal_data = base64_encode($logoPrincipal_blob);
$logoSecundario_data = base64_encode($logoSecundario_blob);
$favicoint_data = base64_encode($favicoin_blob);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel='stylesheet prefetch' href='css/bootstrap.min.css'>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
    <?php
    if (isset($favicoin_blob) && !empty($favicoin_blob)) {
        $base64Image = base64_encode($favicoin_blob);
    } else {
        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
        $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
    } ?>
    <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" class="favicoin"
        type="image/x-icon">
    <link href="css/login.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amelia</title>
</head>


<body>

    <div class="content">
        <div class="container">
            <div class="login-content">
                <img src="img/Logos/Logo.png" alt="">
                <form id="form" autocomplete="off">
                    <h2 class="title">Bienvenido</h2>

                    <div class="form_container">

                        <div class="form_group">
                            <label for="name">Usuario</label>
                            <div class="icono_inicio_login">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" class="form-control" id="name" name="c_usuario" placeholder=" "
                                autocomplete="off">
                        </div>
                        <div class="form_group">
                            <label for="contraseña-actual">Contraseña</label>
                            <div class="icono_inicio_login">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input type="password" class="form-control" placeholder=" " id="contraseña-actual"
                                autocomplete="off">
                            <button type="button" id="toggle-password-visibility">
                                <img src="img/invisible.png" alt="Mostrar/Ocultar contraseña" id="eye-icon">
                            </button><br><br>
                            <span class="form_line"></span>

                            <div class="alert alert-warning" id="ingrese_usuario_contraseña">
                                <strong>¡Error!</strong> Ingrese usuario y contraseña
                            </div>

                            <div class="alert alert-warning" id="ingrese_usuario">
                                <strong>¡Error!</strong> Ingrese Usuario
                            </div>

                            <div class="alert alert-warning" id="ingrese_contraseña">
                                <strong>¡Error!</strong> Ingrese Contraseña
                            </div>

                            <div class="alert alert-danger" id="usuario_inexistente">
                                <strong>¡Error!</strong> Usuario o contraseña son incorrectos.
                            </div>

                            <div class="alert alert-danger" id="contreseña_incorrecta">
                                <strong>¡Error!</strong> Contraseña incorrecta
                            </div>
                            <div class="alert alert-warning" id="Limite">
                                <strong>¡Aviso!</strong> Se bloqueo temporalmente por exceder los limites
                            </div>




                            <div class="alert alert-danger" id="usaurio_inactivo">
                                <strong>¡Error!</strong> Usuario inactivo
                            </div>
                            <div class="animacion_entrar" id="animacion_exito">
                                <div style="--size: 64px; --dot-size: 6px; --dot-count: 6; --color: #fff; --speed: 1s; --spread: 60deg;"
                                    class="dots">
                                    <div style="--i: 0;" class="dot"></div>
                                    <div style="--i: 1;" class="dot"></div>
                                    <div style="--i: 2;" class="dot"></div>
                                    <div style="--i: 3;" class="dot"></div>
                                    <div style="--i: 4;" class="dot"></div>
                                    <div style="--i: 5;" class="dot"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button type="submit" name="entrar" value="Entrar" class="cssbuttons-io-button"> Entrar
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path fill="currentColor"
                                    d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z">
                                </path>
                            </svg>
                        </div>
                    </button>
                    <p data-toggle="modal" data-target="#myModal" class="Olvide_contraseña telefono">¿Olvidaste tu
                        contraseña?</p>
                    <div class="copyright">
                        <img src="img/Logos/Logo.png" alt="" id="copyright_icon">
                        <p>Copyright© 2024. <br> Tecnosolucionext</p>
                        
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class=" modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Restaurar contraseña</h4>
                </div>
                <div class="modal-body">
                    <form id="recuperar-contrasena-form">
                        <div class="alert-info" style="margin: 0px 10px 40px 0px;">
                            <h4>Ingrese su correo de recuperación</h4>
                            <p style="color: #474b4e;opacity: 0.9;">En caso de no recordarlo comuníquese con algún
                                administrador para el reinicio de sus credenciales</p>
                        </div>
                        <input type="email" class="form-control gmail" id="email" name="email" required
                            style="padding: 4px;margin: auto;" autocomplete="off">
                        <div id="Incluplimiento"></div>
                        <p class="formulario__mensaje-exito" id="formulario__mensaje-exito"> <strong>¡Éxito!</strong>
                            ¡Se
                            restableció su contraseña exitosamente!</p>
                        <div class="alert alert-warning" style="display:none;" id="formulario__mensaje">
                            <strong>¡Error!</strong> Por favor rellena los campos correctamente.
                        </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        onclick="location.reload()">Salir</button>
                    <button type="submit" class="btn btn-primary" id="enviar_codigo">Enviar
                        Codigo</button>
                    </form>
                    <button type="button" class="btn btn-warning" id="cambiarcontra" onclick="enviarFormulario()">
                        Cambiar Contraseña
                    </button>

                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
$('#cambiarcontra').hide();
$('#enviar_codigo').prop('disabled', false);
$(document).ready(function() {
    $('#recuperar-contrasena-form').submit(function(event) {
        event.preventDefault(); // Evita que se envíe el formulario de forma normal
        var data = $(this).serialize(); // Obtener los datos del formulario
        $.ajax({
            type: 'POST',
            url: 'Recuperar_contraseña/recuperar_contraseña.php',
            data: data,
            success: function(response) {
                // Si la respuesta es el formulario de verificación, mostrarlo en el modal
                if (response.includes("formulario_verificacion")) {
                    $('#myModal').html(response);
                } else if (response.includes("formulario_cambio_contrasena")) {
                    $('#myModal').html(response);
                } else {
                    $('#Incluplimiento').html(
                    response); // Mostrar la respuesta del servidor en el div con el id 'mensaje'
                }
            }
        });
    });
});
const passwordField = document.querySelector("#contraseña-actual");
// Selecciona el botón de alternar visibilidad de contraseña en el documento HTML y lo almacena en una constante llamada "toggleButton"
const toggleButton = document.querySelector("#toggle-password-visibility");
// Selecciona el icono del ojo en el documento HTML y lo almacena en una constante llamada "eyeIcon"
const eyeIcon = document.querySelector("#eye-icon");

// Agrega un event listener al botón de alternar visibilidad de contraseña para activarse en cada clic
toggleButton.addEventListener("click", function() {
    // Si el tipo del campo de entrada de contraseña es "password", cambia el tipo a "text" y cambia la imagen del icono del ojo a una imagen de ojo abierto
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.src = "img/ojo (1).png";
        // De lo contrario, si el tipo del campo de entrada de contraseña no es "password", cambia el tipo a "password" y cambia la imagen del icono del ojo a una imagen de ojo cerrado
    } else {
        passwordField.type = "password";
        eyeIcon.src = "img/invisible.png";
    }
});
var ingrese_usuario_contraseña = $('#ingrese_usuario_contraseña');
var usuarioInexistente = $('#usuario_inexistente');
var contraseñaIncorrecta = $('#contreseña_incorrecta');
var usuarioInactivo = $('#usaurio_inactivo');
var ingreseUsuario = $('#ingrese_usuario');
var ingrese_contraseña = $('#ingrese_contraseña');
var animacion_exito = $('#animacion_exito');
var Limite = $('#Limite');
// Ocultar mensajes de error previos
animacion_exito.hide();
Limite.hide();
ingrese_usuario_contraseña.hide();
usuarioInexistente.hide();
contraseñaIncorrecta.hide();
usuarioInactivo.hide();
ingreseUsuario.hide();
ingrese_contraseña.hide();
$('#form').on('submit', function(event) {
    event.preventDefault();
    var usuario = $('#name').val();
    var contraseña = $('#contraseña-actual').val();
    $.ajax({
        url: 'json/server_index.php',
        method: 'POST',
        data: {
            c_usuario: usuario,
            c_password: contraseña
        },
        success: function(response) {
            console.log(response);
            $('.alert').hide(); // Ocultar todas las alertas
            if (response.includes('Ingrese Usuario y Contraseña')) {
                $('#ingrese_usuario_contraseña').hide().show();
            } else if (response.includes('Ingrese Usuario')) {
                $('#ingrese_usuario').hide().show();
            } else if (response.includes('Ingrese Contraseña')) {
                $('#ingrese_contraseña').hide().show();
            } else if (response.includes('Usuario Inexistente')) {
                $('#usuario_inexistente').hide().show();
            } else if (response.includes('Contraseña incorrecta')) {
                $('#contreseña_incorrecta').hide().show();
            } else if (response.includes('Limite de intentos.')) {
                $('#Limite').hide().show();
            } else if (response.includes('Usuario Inactivo')) {
                $('#usaurio_inactivo').hide().show();
            } else if (response.includes('Administrador Panteones')) {
                $('#animacion_exito').hide().show();
                setTimeout(function() {
                    // Redirigir después de 5 segundos
                    window.location.href = 'User_Pante/ptn_pante_inicio_tco.php';
                }, 5000); // 5000 milisegundos = 5 segundos
            } else if (response.includes('Administrador Sistemas')) {
                $('#animacion_exito').hide().show();
                setTimeout(function() {
                    // Redirigir después de 5 segundos
                    window.location.href = 'User_Sistem/ptn_sist_inicio.php';
                }, 5000); // 5000 milisegundos = 5 segundos
            } else if (response.includes('Tesoreria')) {
                $('#animacion_exito').hide().show();
                setTimeout(function() {
                    // Redirigir después de 5 segundos
                    window.location.href = 'User_Tesoreria/ptn_sist_inicio.php';
                }, 5000); // 5000 milisegundos = 5 segundos
            } else if (response.includes('Tecnosolucionext')) {
                $('#animacion_exito').hide().show();
                setTimeout(function() {
                    // Redirigir después de 5 segundos
                    window.location.href = 'User_Tecnosolucionext/ptn_tecno_inicio.php';
                }, 5000); // 5000 milisegundos = 5 segundos
            }
        },
        error: function() {
            // Mostrar mensaje de error en caso de fallo en la solicitud AJAX
            alert('Error en la solicitud AJAX');
        }
    });
});
</script>