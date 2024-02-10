<?php
// Desactiva la visualización de errores en la pantalla
ini_set('display_errors', 0);
// Asegúrate de que los errores se escriban en el archivo de log de errores de PHP
ini_set('log_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../conexiones/conexion.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

$correo = $_POST['email'];
$stmt = $mysqli->prepare("SELECT * FROM mxpt_usuarios WHERE Mail = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows == 0) {
?>
    <br>

    <div class="alert-danger">
        <strong>¡Error!</strong> El correo que proporciono no está asociado a ningún usuario.
    </div>
<?php
} else {
?>
    <script>
        // Deshabilitar el botón después de 10 segundos
        setTimeout(function() {
            document.getElementById("enviar_codigo").disabled = true;
        }, 10000);
    </script>
    <?php
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);


    try {
        $numero_aleatorio = rand(100000, 999999);
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'tecnosolucionext2019@gmail.com';                     //SMTP username
        $mail->Password   = 'deyrpdzyasczwbkr';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('tecnosolucionext2019@gmail.com', 'Recuperar Contraseña');
        $mail->addAddress($correo, 'CODEPE');     //Add a recipient

        $mail->AddEmbeddedImage('reparar.png', 'imagen1');

        //Content
        $mail->isHTML(true);
        //Set email format to HTML
        $mail->Subject = 'Cambio Contraseña';
        $mail->Body = '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Document</title>
        <style type="text/css">
        .Contenedor_codigo {
            width: 260px;
            margin: auto;
            
            text-align: center;
            border-radius: 9px;
            color: #aaa;
            font-size: 50px;
            border: 2px solid #e8e8e8;
        }
     
        .form_container {
            width: 400px;
            margin: auto;
            padding-bottom: 40px;
            padding-top: 40px;
            background-color: #e8e8e833;
            text-align:center;
            height: fit-content;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;  
            background-color: #ffffff;
            box-shadow: 0px 106px 42px rgba(0, 0, 0, 0.01),
                0px 59px 36px rgba(0, 0, 0, 0.05), 0px 26px 26px rgba(0, 0, 0, 0.09),
                0px 7px 15px rgba(0, 0, 0, 0.1), 0px 0px 0px rgba(0, 0, 0, 0.1);
            border-radius: 11px;
            font-family: "Inter", sans-serif;
        }
    
        .logo_container {
            box-sizing: border-box;
            text-align: center;
            width: 80px;
            margin: auto;
            height: 80px;
            background: linear-gradient(180deg,rgba(248,248,248,0) 50%,#f8f8f888 100%);
            border: 1px solid #e4e4e4;
            border-radius: 11px;
        }
    
        .title_container {
            flex-direction: column;
            text-align:center;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 30px;
        }
    
        .title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            text-align:center;
            color: #212121;
        }
    
        .subtitle {
            font-size: 13px;
            max-width: 80%;
            text-align: center;
            text-align:center;
            line-height: 1.1rem;
            color: #8B8E98;
            margin: 10px 10px 10px 10px;
        }
    
    
    
        .icon {
            width: 20px;
            position: absolute;
            z-index: 99;
            left: 12px;
            bottom: 9px;
        }
    
        .separator {
            width: 100%;
            text-align:center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            color: #8B8E98;
        }
    
        .separator .line {
            display: block;
            width: 100%;
            height: 1px;
            border: 0;
            background-color: #e8e8e8;
        }
    
        .note {
            font-size: 0.75rem;
            color: #8B8E98;
            text-align:center;
            text-decoration: underline;
        }
    </style>
    
<body>
<div class="form_container">
    <div class="logo_container"><img src="cid:imagen1" alt="img" style="width: 80px;"></div> <br>
    <div class="title_container">
        <p class="title">RECUPERAR CONTRASEÑA</p>
        <br>
        <hr>
        <span class="subtitle">
            Recibimos su solicitud de cambio de contraseña. Por motivos de seguridad, le recomendamos no compartir
            el siguiente código con terceros. Por favor, ingrese el código proporcionado en la ventana
            correspondiente del programa para proceder con el restablecimiento de su contraseña.</span><br>
    </div>
    


    <div class="separator">
        <hr class="line">
        <span></span>
        <hr class="line">
    </div><br>

    <div class="Contenedor_codigo">
    ' . $numero_aleatorio . '
    </div><br>
    <div class="separator">
        <hr class="line">
        <span></span>
        <hr class="line">
    </div><br>
</div>
</body>
</html>';
        $mail->CharSet = 'UTF-8';
        $mail->send();
    } catch (Exception $e) {
    }

    ?>
    <br>
    <script>
        $('#enviar_codigo').hide();
    </script>
    <div class="alert-verde" id="alerta_codigo_mandado">
        <?php
        // Registro de log al enviar correo
        date_default_timezone_set('America/Mexico_City');
        $logFileName = "log_" . date("Y-m-d") . ".txt";
        $logFilePath = "../event_logs/logs/" . $logFileName;

        // Mensaje que se envió un correo
        $logMessage = date("Y-m-d H:i:s") . " - Correo: $correo - Message: Correo de recuperación de contraseña enviado";

        if (file_put_contents($logFilePath, $logMessage . "\n", FILE_APPEND) === false) {
            error_log("Error al escribir en el archivo de log al enviar correo.");
        }

        ?>
        <strong>¡Correcto!</strong> Hemos enviado un correo de recuperación de contraseña a tu dirección de correo
    </div>
    <div class="caja_codigo_verificacion" id="fds">
        <div class="form_group">
            <label for="username-input" style="float: left;">Ingrese código de verificación</label>
            <input type="text" id="username-input" class="form-control" required onkeypress="return solonumeros(event)"><br>
            <div id="codigo-alert" class="alert alert-danger" style="display:none;"></div>
        </div>
    </div>
    </div>
<?php
}
?>

<div id="formulario-cambio" style="display:none;">

    <form id="formulario">
        <input type="hidden" name="correo" value="<?php echo $correo ?>">
        <!-- Grupo: Contraseña -->
        <div class="formulario__grupo" id="grupo__pass1">
            <label for="pass1" class="formulario__label">Contraseña</label>
            <div class="formulario__grupo-input">
                <input type="text" required class="formulario__input" name="pass1" id="pass1">
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
    </form>
</div>



<div class="formulario__mensaje" id="formulario__mensaje" style="padding: 10px;">
    <p><i class="fas fa-exclamation-triangle"></i> <b>Error:</b> Por favor rellena el formulario correctamente. </p>
</div>




<script>
    function solonumeros(e) {
        key = e.keyCode || e.which;
        teclado2 = String.fromCharCode(key).toLowerCase();
        numero = "0123456789"; // Lista de números
        especiales = "8-37-38-46"; // Códigos de teclas especiales
        teclado_especiall = false;
        for (var i in especiales) {
            if (key == especiales[i]) {
                teclado_especiall = true;
                break;
            }
        }
        if (numero.indexOf(teclado2) == -1 && !teclado_especiall) {
            return false; // Evita que se ingrese el carácter si no es un número o una tecla especial permitida
        }

        // Validación para permitir solo 10 dígitos
        input = e.target;
        if (input.value.length >= 6) {
            return false; // Evita que se ingresen más de 10 dígitos
        }
    }
    $('#enviar_codigo').prop('disabled', true);

    $(document).ready(function() {
        // Controlador de eventos de cambio para el campo de entrada de usuario
        $('#username-input').change(function() {
            // Obtener el valor ingresado por el usuario
            var codigo = $(this).val();

            // Verificar si el código ingresado no está vacío y es igual al número aleatorio generado en PHP
            if (codigo !== '' && codigo == <?php echo $numero_aleatorio; ?>) {
                // Mostrar la alerta de código correcto y habilitar el botón de continuar
                $('#codigo-alert').removeClass('alert-danger').addClass('alert-warning').html(
                    '<strong>Código correcto!</strong> Continúa para completar tu registro.').show();
                $('#enviar_codigo').prop('disabled', false);
                $('#formulario-cambio').show();
                $('#ocultarcontraseña2').hide();
                $('#cambiarcontra').show();
                $('#fds').hide();
                $('#enviar_codigo').hide();
            } else {
                // Mostrar la alerta de código incorrecto y deshabilitar el botón de continuar
                $('#codigo-alert').removeClass('alert-success').addClass('alert-danger').html(
                    '<strong>¡Código incorrecto!</strong> Por favor, verifica que ingresaste el código correcto.'
                ).show();
                $('#enviar_codigo').prop('disabled', true);
                $('#formulario-cambio').hide();
            }

            // Ocultar la alerta de código mandado
            $('#alerta_codigo_mandado').hide();
        });
    });





    const formulario = document.getElementById('formulario');
    const inputs = document.querySelectorAll('#formulario input');
    const expresiones = {
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,10}$/, // letras mayusculas, minusculas, numeros y simbolos seguros, de 8 a 20 caracteres.
    }

    const campos = {
        password: false,
    }

    const validarFormulario = (e) => {
        switch (e.target.name) {

            case "pass1":
                validarCampo(expresiones.password, e.target, 'pass1');
                validarPassword2();
                break;
            case "pass2":
                validarPassword2();
                break;

        }
    }


    const validarCampo = (expresion, input, campo) => {
        if (expresion.test(input.value)) {
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-check-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-times-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.remove(
                'formulario__input-error-activo');
            campos[campo] = true;
            $('#ocultarcontraseña2').show();
        } else {
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos[campo] = false;
            $('#ocultarcontraseña2').hide();
        }
    }

    const validarPassword2 = () => {
        const inputPassword1 = document.getElementById('pass1');
        const inputPassword2 = document.getElementById('pass2');

        if (inputPassword1.value === '' && inputPassword2.value === '') {
            // Ambos campos están vacíos
            document.getElementById(`grupo__pass1`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass1`).classList.remove('formulario__grupo-correcto');
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__pass1 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__pass1 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__pass1 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos['password'] = false;
            $('#formulario__mensaje').hide();
            return;
        }

        if (inputPassword1.value !== inputPassword2.value) {
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos['password'] = false;

            $('#formulario__mensaje').hide();

        } else {
            document.getElementById(`grupo__pass2`).classList.remove('formulario__grupo-incorrecto');
            document.getElementById(`grupo__pass2`).classList.add('formulario__grupo-correcto');
            document.querySelector(`#grupo__pass2 i`).classList.remove('fa-times-circle');
            document.querySelector(`#grupo__pass2 i`).classList.add('fa-check-circle');
            document.querySelector(`#grupo__pass2 .formulario__input-error`).classList.remove(
                'formulario__input-error-activo');
            campos['password'] = true;

            $('#formulario__mensaje').hide();

        }
    }


    inputs.forEach((input) => {
        input.addEventListener('keyup', validarFormulario);
        input.addEventListener('blur', validarFormulario);
    });


    function enviarFormulario() {
        if (campos.password) {


            document.getElementById('formulario__mensaje-exito').classList.add('formulario__mensaje-exito-activo');

            $('#formulario-cambio').hide();
            $('#formulario__mensaje').hide();
            $('#fds').hide();

            $('#cambiarcontra').hide();
            document.querySelectorAll('.formulario__grupo-correcto').forEach((icono) => {
                icono.classList.remove('formulario__grupo-correcto');
            });

            var formData = new FormData(formulario); // Obtener los datos del formulario
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Procesar la respuesta del servidor
                    if (this.responseText == 'ok') {
                        // Mostrar alerta de éxito
                        document.getElementById('formulario__mensaje-exito').classList.add(
                            'formulario__mensaje-exito-activo');

                    } else {
                        // Mostrar alerta de error
                        document.getElementById('formulario__mensaje').classList.add(
                            'formulario__mensaje-activo');
                    }
                }
            };

            xmlhttp.open("POST", "Recuperar_contraseña/cambiar_contraseña.php", true);
            xmlhttp.send(formData); // Enviar los datos del formulario al servidor
        } else {
            document.getElementById('formulario__mensaje').classList.add('formulario__mensaje-activo');
        }
    }
</script>