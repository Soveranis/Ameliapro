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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <?php
    if (isset($favicoin_blob) && !empty($favicoin_blob)) {
        $base64Image = base64_encode($favicoin_blob);
    } else {
        // Si $logoPrincipal_blob no está definido o está vacío, usa una URL por defecto
        $base64Image = base64_encode(file_get_contents('../img/Favicoin.png'));
    } ?>
    <link rel="shortcut icon" href="data:image/jpeg;base64,<?php echo $base64Image; ?>" type="image/x-icon">
    <link rel="stylesheet" href="../css/General.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <div id="wrapper">

        <!-- Agregamos el menu lateral con los links respectivos -->
        <?php include('../Vistas/Menu_Lateral_user_pante.php'); ?>

        <div class="main" style="padding-top:120px;">
            <!-- MAIN CONTENT -->
            <div class="main-content" style="padding-top:0px;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="demo-area-chart" class="ct-chart">
                                        <h2>Manual Usuario</h2>

                                        <ul class="galeria">
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Titulares</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>
                                                    </div>
                                                </div>

                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Finados</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>


                                                    </div>
                                                </div>

                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Tumbas</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>

                                                    </div>
                                                </div>

                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Pagos</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>


                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Consultas</p>
                                                    </div>
                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>
                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Codigos postales</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>


                                                    </div>
                                                </div>

                                            </li>
                                            <li>
                                                <div class="card">
                                                    <div class="header">
                                                        <span class="icon_galeria">
                                                            <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path clip-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" fill-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                        <p class="alert">Servicios</p>
                                                    </div>

                                                    <p class="message">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam
                                                        ea quo unde
                                                        vel adipisci blanditiis voluptates eum. Nam, cum minima?
                                                    </p>

                                                    <div class="actions">
                                                        <a class="read" href="">
                                                            Visitar
                                                        </a>


                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
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
    </div>
</body>

</html>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
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
</script>