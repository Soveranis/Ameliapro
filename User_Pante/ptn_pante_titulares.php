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
                                        <h3>Administrador Titulares</h3>
                                        <hr><br><br>

                                        <button class="continue-application" data-toggle="modal" data-target="#myModal_nuevo_titular">
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
                                            Nuevo Titular
                                        </button>

                                        <div class="table-responsive">
                                            <table class=" table-bordered table-striped" name="tbl-contact" id="tbl-contact">
                                                <caption class="caption table-bordered table-striped">Administrador de
                                                    Titulares</caption>
                                                <thead>
                                                
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Nombre Titular</th>
                                                        <th>Apellido Paterno</th>
                                                        <th>Apellido Materno</th>
                                                        <th>Teléfono Titular</th>
                                                        <th>Telefono Casa</th>
                                                        <th>Dirección</th>
                                                        <th>Responsable</th>
                                                        <th>Fecha Captura</th>
                                                        <th>Opciones</th>

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




















<!-- Modal editor metodo 2 -->
<div class="modal fade" id="modal_version_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Editar Datos del Titular</h4>
            </div>
            <div class="modal-body">
                <div class="modal_cont_medium">
                    <form id="Formulario_editar_m2" autocomplete="off">

                        <h4>Datos del titular</h4>
                        <input type="hidden" id="Identificador_m2_editar" name="Identificador_m2_editar">
                        <div style="display:flex; gap: 22px;">
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" id="Nombre_titular_m2_editar" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Paterno</label>
                                <input type="text" id="Apellido_Paterno_m2_editar" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Materno</label>
                                <input type="text" id="Apellido_Materno_m2_editar" class="form-control" required onkeypress="return sololetras(event)">
                            </div>
                        </div><br>
                        <div style="display:flex; gap:30px; margin:auto;">
                            <div class="form-grup">
                                <label for="">Teléfono</label>
                                <input type="text" id="telefono_editar_m2_editar" class="form-control" required onkeypress="return solonumeros(event)" style="width: 300px;">
                            </div>

                            <div class="form-grup">
                                <label for="">Teléfono Casa (Opcional)</label>
                                <input type="text" id="telefono_casa_m2_editar" class="form-control" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>

                        <div class="form-group">
                            <h4>Dirección del titular</h4>

                            <textarea class="form-control" id="Direccion_m2_editar" readonly=""></textarea>
                            <div style="display: flex; gap: 20px;">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="checkDireccion_2" onchange="deseleccionarOtroCheckbox_2(this)">Modificar Dirección
                                    </label>
                                </div>
                                <div class="checkbox" id="conte_m2_manual" style="display:none;">
                                    <label>
                                        <input type="checkbox" id="agregar_manualmente_m2" onchange="deseleccionarOtroCheckbox_2(this)">Agregar Manualmente
                                    </label>
                                </div>
                            </div>
                            <div id="text_area_m2_editar" style="display:none">
                                <div class="bs-callout bs-callout-warning">
                                    <h4>¡Aviso!</h4> Por favor, ingresa la dirección en el siguiente orden: Estado,
                                    Municipio, Barrio
                                </div>

                                <textarea class="form-control" id="Agregar_manualmente_editar_metodo_2" name="Agregar_manualmente_editar_metodo_2" cols="30" rows="10"></textarea>
                            </div>
                            <br>


                            <div class="formulario_api_direccion" id="oculto_2">


                                <div class="form-group" id="buscador_m2_editar">
                                    <h4>Busque dirección del titular</h4>
                                    <div class="form">
                                        <label for="search">
                                            <input id="metodo_local_funcion_metodo_2" type="text">
                                            <div class="icon_buscador">
                                                <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                                </svg>

                                            </div>

                                        </label>
                                    </div>
                                    <input type="hidden" id="Direccion_completa_metodo_2" name="Direccion_completa_metodo_2">
                                </div>


                                <div style="display:flex; gap:10px">
                                    <div class="form-group">
                                        <label for="Calle">Calle</label>
                                        <input type="text" class="form-control" name="calle_2" id="calle_2">
                                    </div>
                                    <div class="form-group">
                                        <label for="Calle">No. Exterior</label>
                                        <input type="text" class="form-control" name="Nr.Exterior" id="Nr_Exterior_editar_2" onkeypress="return solonumeros(event)">
                                    </div>
                                    <div class="form-group">
                                        <label for="Calle">No. Interior</label>
                                        <input type="text" class="form-control" name="Nr.Interior" id="Nr_Interior_editar_2" onkeypress="return solonumeros(event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="alert alert-verde" id="titular_edicion_exitoso_2" style="display:none;">
                            <strong>¡Genial!</strong> Registro actualizado exitosamente.
                        </div>


                        <div class="alert alert-warning" id="titular_edicion_error_2" style="display:none;">
                            <strong>¡Errorl!</strong> Algo salió mal
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="submit" class="btn btn-primary" id="guardar_metodo_2">Guardar</button>
            </div>

            </form>
        </div>
    </div>
</div>


<!-- Modal editar metodo 1 -->
<div class="modal fade" id="myModal_editar_titular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Editar Datos del Titular</h4>
            </div>
            <div class="modal-body">
                <div class="modal_cont_medium">
                    <form id="Editar_titular" autocomplete="off">

                        <h4>Datos del titular</h4>
                        <input type="hidden" id="Identificador_metodo_1" name="Identificador_metodo_1">
                        <div style="display:flex; gap: 22px;">
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" id="Nombre_titular_editar_metodo_1" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Paterno</label>
                                <input type="text" id="Apellido_Paterno_editar_metodo_1" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Materno</label>
                                <input type="text" id="Apellido_Materno_editar_metodo_1" class="form-control" required onkeypress="return sololetras(event)">
                            </div>
                        </div><br>
                        <div style="display:flex; gap:30px; margin:auto;">
                            <div class="form-grup">
                                <label for="">Teléfono</label>
                                <input type="text" id="telefono_editar_metodo_1" class="form-control" required onkeypress="return solonumeros(event)" style="width: 300px;">
                            </div>

                            <div class="form-grup">
                                <label for="">Teléfono Casa (Opcional)</label>
                                <input type="text" id="telefono_casa_metodo_1" class="form-control" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>

                        <div class="form-group">
                            <h4>Dirección del titular</h4>

                            <textarea class="form-control" id="Direccion_editar_metodo_1" readonly=""></textarea>

                            <div style="display: flex; gap: 20px;">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="checkDireccion" onchange="deseleccionarOtroCheckbox(this)">Modificar Dirección
                                    </label>
                                </div>
                                <div class="checkbox" id="conten_check" style="display:none;">
                                    <label>
                                        <input type="checkbox" id="Agregar_manualmente_editar_check" onchange="deseleccionarOtroCheckbox(this)">Agregar Manualmente
                                    </label>
                                </div>
                            </div>
                            <div id="text_area" style="display:none">
                                <div class="bs-callout bs-callout-warning">
                                    <h4>¡Aviso!</h4> Por favor, ingresa la dirección en el siguiente orden: Estado,
                                    Municipio, Barrio
                                </div>
                                <textarea class="form-control" id="Agregar_manualmente_editar_metodo_1" name="Agregar_manualmente_editar_metodo_1" cols="30" rows="10"></textarea>
                            </div>

                            <div class="formulario_api_direccion" id="oculto">


                                <div class="bs-callout bs-callout-warning">
                                    <h4>¡Aviso!</h4> Si no cuentas con los datos de Número Exterior o Número
                                    Interior, deja vacíos los campos.
                                </div><br>
                                <label for="Estado">Estado:</label>
                                <select class="form-control" id="Estado_editar">
                                    <option value="">Selecciona un estado</option>
                                </select>

                                <label for="Municipio">Municipio:</label>
                                <select class="form-control" id="Municipio_editar">
                                    <option value="">Selecciona un municipio</option>
                                </select>

                                <label for="Colonia">Colonia:</label>
                                <select class="form-control" id="Colonia_editar">
                                    <option value="">Selecciona una colonia</option>
                                </select>

                                <input type="hidden" id="EstadoNombre_editar" name="EstadoNombre_editar">
                                <input type="hidden" id="MunicipioNombre_editar" name="MunicipioNombre_editar">
                                <input type="hidden" id="CodigoPostal_editar" name="CodigoPostal_editar">
                                <input type="hidden" id="ColoniaNombre_editar" name="ColoniaNombre_editar">
                            </div>
                            <div style="display:flex; gap:10px" id="Datos_calle_editar_check">


                                <div class="form-group">

                                    <label for="Calle">Calle</label>

                                    <input type="text" class="form-control" id="calle_editar_bien">
                                </div>
                                <div class="form-group">

                                    <label for="Calle">No. Exterior</label>
                                    <input type="text" class="form-control" name="Nr.Exterior" id="Nr_Exterior_editar" onkeypress="return solonumeros(event)">
                                </div>

                                <div class="form-group">

                                    <label for="Calle">No. Interior</label>
                                    <input type="text" class="form-control" name="Nr.Interior" id="Nr_Interior_editar" onkeypress="return solonumeros(event)">
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="alert alert-verde" id="titular_edicion_exitoso">
                            <strong>¡Genial!</strong> Registro actualizado exitosamente.
                        </div>


                        <div class="alert alert-danger" id="titular_edicion_error">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-primary" id="guardar_metodo_1">Guardar</button>
            </div>

            </form>
        </div>
    </div>
</div>



<!-- Modal metodo 2 -->
<div class="modal fade" id="nuevo_titular_metodo_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Alta de Nuevo titulares</h4>
            </div>
            <div class="modal-body">
                <div class="modal_cont_medium">
                    <form id="Metodo2" autocomplete="off">

                        <h4>Datos del titular</h4>
                        <div style="display:flex; gap: 22px;">
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" id="Nombre_titular_metodo2" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Paterno</label>
                                <input type="text" id="Apellido_Paterno_editar_metodo_2" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Materno</label>
                                <input type="text" id="Apellido_Materno_editar_metodo_2" class="form-control" required onkeypress="return sololetras(event)">
                            </div>
                        </div><br>
                        <div style="display:flex; gap:30px; margin:auto;">
                            <div class="form-grup">
                                <label for="">Teléfono</label>
                                <input type="text" id="Telefono_titular_metodo_2" class="form-control" required onkeypress="return solonumeros(event)" style="width: 300px;">
                            </div>

                            <div class="form-grup">
                                <label for="">Teléfono Casa (Opcional) </label>
                                <input type="text" id="telefono_casa_metodo_2" class="form-control" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>


                        <div class="form-group" id="Ventana_api_direcciones_modulo2">
                            <h4>Busque dirección del titular</h4>
                            <div class="form">
                                <label for="search">
                                    <input id="Buscardor_metodo_2_altas" type="text" required>
                                    <div class="icon_buscador">
                                        <svg stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="swap-on">
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linejoin="round" stroke-linecap="round"></path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            <input type="hidden" id="Direccion_completa">
                            <!-- Resto del formulario... -->
                            <div class="bs-callout bs-callout-warning">
                                <h4>¡Aviso!</h4> Si no cuentas con los datos de Número Exterior o Número
                                Interior, deja vacíos los campos.
                            </div>
                        </div>

                        <div class="checkbox" id="conten_check_2">
                            <label>
                                <input type="checkbox" id="Agregar_manualmente_editar_check_2" onchange="deseleccionarOtroCheckbox(this)">Agregar Manualmente
                            </label>
                        </div>



                        <div id="Manualmente_metodo_2" style="display:none;">
                            <div class="bs-callout bs-callout-warning">
                                <h4>¡Aviso!</h4> Por favor, ingresa la dirección en el siguiente orden: Estado,
                                Municipio, Barrio
                            </div>

                            <textarea id="text_area_metodo_2" cols="30" rows="10" class="form-control"></textarea> <br><br>
                        </div>

                        <div style="display:flex; gap:10px">


                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">Calle</label>

                                <input type="text" class="form-control" name="calle" id="calle_metodo2" required>
                            </div>
                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">No. Exterior</label>
                                <input type="text" class="form-control" name="Nr.Exterior" id="Nr_Exterior_metodo_2" onkeypress="return solonumeros(event)">
                            </div>

                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">No. Interior</label>
                                <input type="text" class="form-control" name="Nr.Interior" id="Nr_Interior_metodo2" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>

                        <div class="alert alert-verde" id="titular_exitoso_metodo_2" style="display:none;">
                            <strong>¡Genial!</strong> Registro agregado exitosamente.
                        </div>


                        <div class="alert alert-warning" id="titular_error_metodo_2" style="display:none;">
                            <strong>¡Errorl!</strong> Algo salió mal.
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-primary">Guardar</button>
            </div>

            </form>
        </div>
    </div>
</div>


<!-- Modal Metodo 1 -->
<div class="modal fade" id="myModal_nuevo_titular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Alta de Nuevo titulares</h4>
            </div>
            <div class="modal-body">
                <div class="modal_cont_medium">
                    <form id="Nuevo_titular" autocomplete="off">

                        <h4>Datos del titular</h4>

                        <div style="display:flex; gap: 22px;">
                            <div class="form-grup">
                                <label for="">Nombre Titular</label>
                                <input type="text" id="Nombre_titular" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Paterno</label>
                                <input type="text" id="Apellido_Paterno" class="form-control" required onkeypress="return sololetras(event)">
                            </div>

                            <div class="form-grup">
                                <label for="">Apellido Materno</label>
                                <input type="text" id="Apellido_Materno" class="form-control" required onkeypress="return sololetras(event)">
                            </div>
                        </div><br>
                        <div style="display:flex; gap:30px; margin:auto;">
                            <div class="form-grup">
                                <label for="">Teléfono</label>
                                <input type="text" id="Telefono_titular" class="form-control" required onkeypress="return solonumeros(event)" style="width: 300px;">
                            </div>

                            <div class="form-grup">
                                <label for="">Teléfono Casa (Opcional)</label>
                                <input type="text" id="Telefono_casa" class="form-control" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>



                        <div class="form-group" id="Direccion_titular_metodo_1">
                            <h4>Dirección del titular</h4>
                            <label for="Estado">Estado:</label>
                            <select class="form-control" id="Estado">
                                <option value="">Selecciona un estado</option>
                            </select>

                            <label for="Municipio">Municipio:</label>
                            <select class="form-control" id="Municipio">
                                <option value="">Selecciona un municipio</option>
                            </select>

                            <label for="Colonia">Colonia:</label>
                            <select class="form-control" id="Colonia">
                                <option value="">Selecciona una colonia</option>
                            </select>

                            <input type="hidden" id="EstadoNombre" name="EstadoNombre">
                            <input type="hidden" id="MunicipioNombre" name="MunicipioNombre">
                            <input type="hidden" id="ColoniaNombre" name="ColoniaNombre">
                            <input type="hidden" id="CodigoPostal" name="CodigoPostal">

                            <div class="bs-callout bs-callout-warning" id="callout-stacked-modals">
                                <h4>¡Aviso!</h4> Si no cuentas con los datos de Número Exterior o Número
                                Interior, deja vacíos los campos.
                            </div>
                            <!-- Resto del formulario... -->
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="Ingresar_Manualmente" class="Ingresar_Manualmente"> Ingresar
                                Direccion Manualmente
                            </label>
                        </div>
                        <div id="Contenedor_Direccion_manual" style="display:none;">
                            <div class="bs-callout bs-callout-warning" id="">
                                <h4>¡Aviso!</h4> Por favor, ingresa la dirección en el siguiente orden: Estado,
                                Municipio, Barrio
                            </div>


                            <textarea class="form-control" cols="20" rows="2" id="Direccion_manual" name="Direccion_manual"></textarea>

                        </div><br>



                        <div style="display:flex; gap:10px">


                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">Calle</label>

                                <input type="text" class="form-control" name="calle" id="calle" required>
                            </div>
                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">No. Exterior</label>
                                <input type="text" class="form-control" name="Nr.Exterior" id="Nr_Exterior" onkeypress="return solonumeros(event)">
                            </div>

                            <div class="form-group">
                                <!-- Aquí -->
                                <label for="Calle">No. Interior</label>
                                <input type="text" class="form-control" name="Nr.Interior" id="Nr_Interior" onkeypress="return solonumeros(event)">
                            </div>
                        </div>
                        <hr>

                        <div class="alert alert-verde" id="titular_exitoso">
                            <strong>¡Genial!</strong> Registro agregado exitosamente.
                        </div>


                        <div class="alert alert-danger" id="titular_error">
                            <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                            - Verifica tu conexión a internet.
                            - Actualiza la página o la aplicación.
                            - Cierra sesión y vuelve a iniciar sesión.
                            Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-primary">Guardar</button>
            </div>

            </form>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade bs-example-modal-sm" id="opciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body">

                <div class="form-grup metodos" id="metodo_1_modal_editar">
                    <img src="../img/male-user-edit_25348.png" class="opciones" alt="">
                    <button class="continue-application" data-toggle="modal" data-dismiss="modal" data-target="#myModal_editar_titular" style="margin-top:10px; height: 49px;">
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
                        Editar
                    </button>
                </div>

                <div class="form-grup metodos" id="metodo_2_modal_editar">
                    <img src="../img/change-contract-owner.png.img.png" class="opciones" alt="">
                    <button class="continue-application" style="margin-top:10px; height: 49px;" data-toggle="modal" data-dismiss="modal" data-target="#modal_version_2">
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
                        Editar
                    </button>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
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
            "responsive": true,
            "serverSide": true,
            "ajax": "../json/server_titulares.php",
            "columnDefs": [{
                    "targets": 0,
                    "data": null,
                    "orderable": true,
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
                var Usuario = rowData[7];
                var fechaCaptura = rowData[8];
                var Opciones = rowData[9];
                row.child('<div style="display: flex;"><span class="fecha-info">Fecha de Captura: ' +
                    fechaCaptura +
                    '</span><span class="fecha-info">Opciones: ' +
                    Opciones +
                    '</span></div>').show();

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







    var titular_error = $('#titular_error');
    var titular_exitoso = $('#titular_exitoso');
    var titular_edicion_error = $('#titular_edicion_error');
    var titular_edicion_exitoso = $('#titular_edicion_exitoso');

    // Ocultar mensajes de error previos
    titular_error.hide();
    titular_exitoso.hide();
    titular_edicion_error.hide();
    titular_edicion_exitoso.hide();
    // Aqui empieza el metodo 2 de uso de datos en la base de datos en caso de error de la api

    $('#metodo_2_modal_editar').hide();
    $('#metodo_1_modal_editar').show();


    $(document).on('click', '.recuperar_datos', function() {
        // Datos originales
        var nombre = $(this).data('nombre');
        var apellido_paterno = $(this).data('apellido_paterno');
        var apellido_materno_editar = $(this).data('apellido_materno');
        var telefono = $(this).data('telefono');
        var telefono_casa = $(this).data('telefono_casa');
        var direccion = $(this).data('direccion');
        var id_titular = $(this).data('id');


        // Datos con sufijo _metodo2
        var nombre_metodo2 = $(this).data('nombre_metodo2');
        var apellido_paterno_metodo2 = $(this).data('apellido_paterno_metodo2');
        var apellido_materno_metodo2 = $(this).data('apellido_materno_metodo2');
        var telefono_metodo2 = $(this).data('telefono_metodo2');
        var telefono_casa_metodo2 = $(this).data('telefono_casa_metodo2');
        var direccion_metodo2 = $(this).data('direccion_metodo2');
        var id_metodo2 = $(this).data('id_metodo2');


        $('#Nombre_titular_editar_metodo_1').val(nombre);
        $('#Apellido_Paterno_editar_metodo_1').val(apellido_paterno);
        $('#Apellido_Materno_editar_metodo_1').val(apellido_materno_editar);
        $('#telefono_editar_metodo_1').val(telefono);
        $('#telefono_casa_metodo_1').val(telefono_casa);
        $('#Direccion_editar_metodo_1').val(direccion);
        $('#Identificador_metodo_1').val(id_titular);





        $('#Nombre_titular_m2_editar').val(nombre_metodo2);
        $('#Apellido_Paterno_m2_editar').val(apellido_paterno_metodo2);
        $('#Apellido_Materno_m2_editar').val(apellido_materno_metodo2);
        $('#telefono_editar_m2_editar').val(telefono_metodo2);
        $('#telefono_casa_m2_editar').val(telefono_casa_metodo2);
        $('#Direccion_m2_editar').val(direccion_metodo2);
        $('#Identificador_m2_editar').val(id_metodo2);

    });



    $('#myModal_editar_titular').on('hidden.bs.modal', function() {
        // Resetea el formulario dentro del modal
        $('#Editar_titular')[0].reset();
        // Oculta las alertas
        $('#titular_edicion_exitoso').hide();
        $('#text_area').hide();
        $('#titular_edicion_error').hide();
        $('#titular_edicion_error').hide();
        // Si tienes campos adicionales que necesitan ser reseteados, puedes hacerlo aquí
        // Por ejemplo, si quieres ocultar el div con ID 'oculto'
        $('#oculto').hide();
        $('#Datos_calle_editar_check').hide();
        $('#calle_editar_bien, #Agregar_manualmente_editar_metodo_1').removeAttr('required');
        $('#conten_check').hide();
        $('#Agregar_manualmente_editar_metodo_2').hide();
        $('#guardar_metodo_1').show();
    });

    $('#modal_version_2').on('hidden.bs.modal', function() {
        $('#').removeAttr('required');
        $('#oculto_2').hide();
        $('#conte_m2_manual').hide();
        $('#text_area_m2_editar').hide();
        $('#titular_edicion_exitoso').hide();
        $('#calle_2, #metodo_local_funcion_metodo_2, #calle_editar_bien, #Colonia_editar, #Municipio_editar, #Estado_editar, Agregar_manualmente_editar_metodo_2').removeAttr('required');
        // Oculta las alertas
        $('#titular_edicion_error_2').hide();
        $('#titular_edicion_exitoso_2').hide();

        // Si tienes campos adicionales que necesitan ser reseteados, puedes hacerlo aquí
        // Por ejemplo, si quieres ocultar el div con ID 'oculto'
        $('#oculto').hide();
        $('#guardar_metodo_2').show();
    });



























    $(function() {
        $("#metodo_local_funcion_metodo_2")
            .autocomplete({ // inicializa la función autocomplete en el elemento con ID "curso"
                source: "../json/codigos_postales_metodo_2.php", // indica el archivo PHP donde se realizará la búsqueda
                minLength: 1, // indica la cantidad mínima de caracteres que deben ser ingresados antes de mostrar sugerencias
                select: function(event,
                    ui
                ) { // establece la función que se ejecutará cuando el usuario seleccione una sugerencia
                    event.preventDefault(); // evita que la acción predeterminada se lleve a cabo
                    $('#Direccion_completa_metodo_2').val(ui.item
                        .Direccion_completa_metodo_2
                    ); // establece el valor del elemento cfvvon ID "c_lote" con el valor de la propiedad "c_lote" del objeto seleccionado
                }
            });
    });

    function deseleccionarOtroCheckbox_2(checkbox) {
        if (checkbox.id === "checkDireccion_2") {
            // Si se selecciona "Modificar Dirección", deselecciona "Agregar Manualmente"
            document.getElementById("agregar_manualmente_m2").checked = false;
        } else if (checkbox.id === "agregar_manualmente_m2") {
            // Si se selecciona "Agregar Manualmente", deselecciona "Modificar Dirección"
            document.getElementById("checkDireccion_2").checked = false;
        }
    }

    // METODO 2

    $('#checkDireccion_2').change(function() {
        if ($(this).is(':checked')) {
            $('#oculto_2').show();
            $('#buscador_m2_editar').show();
            $('#conte_m2_manual').show();
            $('#text_area_m2_editar').hide();
            $('#titular_edicion_exitoso').hide();
            $('#calle_2, #metodo_local_funcion_metodo_2').attr('required', true);
            $('#Agregar_manualmente_editar_metodo_2').attr('required', false);
        } else {
            $('#oculto_2').hide();
            $('#conte_m2_manual').hide();
            $('#text_area_m2_editar').hide();
            $('#titular_edicion_exitoso').hide();
            $('#calle_2, #metodo_local_funcion_metodo_2').removeAttr('required');
        }
    });

    $('#agregar_manualmente_m2').change(function() {
        if ($(this).is(':checked')) {
            $('#buscador_m2_editar').hide();
            $('#conte_m2_manual').hide();
            $('#text_area_m2_editar').show();
            $('#metodo_local_funcion_metodo_2').attr('required', false);
            $('#Agregar_manualmente_editar_metodo_2').attr('required', true);
        } else {
            $('#buscador_m2_editar').show();
            $('#Agregar_manualmente_editar_metodo_2').attr('required', false);
            $('#metodo_local_funcion_metodo_2, #Agregar_manualmente_editar_metodo_2').removeAttr('required');
        }
    });



    $('#Formulario_editar_m2').on('submit', function(event) {
        event.preventDefault();

        var id_titular = $('#Identificador_m2_editar').val();
        var Nombre_titular = $('#Nombre_titular_m2_editar').val();
        var Apellido_Paterno_m2_editar = $('#Apellido_Paterno_m2_editar').val();
        var Apellido_Materno_m2_editar = $('#Apellido_Materno_m2_editar').val();
        var Direccion_actual = $('#Direccion_m2_editar').val();
        var telefono_editar_m2_editar = $('#telefono_editar_m2_editar').val();
        var telefono_casa = $('#telefono_casa_m2_editar').val();

        var formData = {
            id_titular: id_titular,
            Nombre_titular: Nombre_titular,
            Apellido_Paterno_m2_editar: Apellido_Paterno_m2_editar,
            Apellido_Materno_m2_editar: Apellido_Materno_m2_editar,
            Direccion_actual: Direccion_actual,
            telefono_editar_m2_editar: telefono_editar_m2_editar,
            telefono_casa: telefono_casa // Corregir el nombre de la variable
        };

        if ($('#checkDireccion_2').is(':checked')) {
            // Construir la dirección basada en la dirección predeterminada
            var Direccion_completa_metodo_2 = $('#Direccion_completa_metodo_2').val();
            var calle_av = $('#calle_2').val();
            var Nr_Interior_editar = $('#Nr_Interior_editar_2').val() || 'S/N';
            var Nr_Exterior_editar = $('#Nr_Exterior_editar_2').val() || 'S/N';
            var Direccion = Direccion_completa_metodo_2 + " " + calle_av + ", No. Interior: " + Nr_Interior_editar +
                ", No. Exterior: " +
                Nr_Exterior_editar;

            formData.Direccion = Direccion;
        } else if ($('#agregar_manualmente_m2').is(':checked')) {
            // Construir la dirección basada en la selección del otro select
            var metodo_m3 = $('#Agregar_manualmente_editar_metodo_2').val();
            var calle_av = $('#calle_2').val();
            var Nr_Interior_editar = $('#Nr_Interior_editar_2').val() || 'S/N';
            var Nr_Exterior_editar = $('#Nr_Exterior_editar_2').val() || 'S/N';
            var Direccion = metodo_m3 + " " + calle_av + ", No. Interior: " + Nr_Interior_editar +
                ", No. Exterior: " +
                Nr_Exterior_editar;

            formData.Direccion = Direccion;
        } else {
            formData.Direccion = Direccion_actual;
        }

        // alert(JSON.stringify(formData));
        $.ajax({
            url: 'Editar/Editar_titular_metodo2.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log(response);

                $('.alert').hide();
                if (response.includes('exito')) {
                    $('#titular_edicion_exitoso_2').hide().show();
                    $('#tbl-contact').DataTable().ajax.reload();
                    $('#Formulario_editar_m2')[0].reset();
                    $('#guardar_metodo_2').hide();
                } else if (response.includes('error')) {
                    $('#titular_edicion_error_2').hide().show();
                    // Mostrar mensaje de error específico aquí
                    // Puedes analizar el contenido de "response" para obtener más detalles del error.
                    console.error('Error en la solicitud AJAX:', response);
                    $('#tbl-contact').DataTable().ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", status, error);
                // Mostrar mensaje de error al usuario si es necesario
                $('#mensaje_de_error').text("Hubo un error en la solicitud AJAX. Detalles: " + error);
            }
        });
    });

    $(function() {
        $("#Buscardor_metodo_2_altas")
            .autocomplete({ // inicializa la función autocomplete en el elemento con ID "curso"
                source: "../json/server_recuperar_datos_codigos_postales.php", // indica el archivo PHP donde se realizará la búsqueda
                minLength: 1, // indica la cantidad mínima de caracteres que deben ser ingresados antes de mostrar sugerencias
                select: function(event,
                    ui
                ) { // establece la función que se ejecutará cuando el usuario seleccione una sugerencia
                    event.preventDefault(); // evita que la acción predeterminada se lleve a cabo
                    $('#Direccion_completa').val(ui.item
                        .Direccion_completa
                    ); // establece el valor del elemento con ID "c_lote" con el valor de la propiedad "c_lote" del objeto seleccionado
                }
            });
    });

    $('#Agregar_manualmente_editar_check_2').change(function() {
        if (this.checked) {
            $('#Ventana_api_direcciones_modulo2').hide();
            $('#Buscardor_metodo_2_altas').removeAttr('required');
        } else {
            $('#Ventana_api_direcciones_modulo2').show();
            $('#Buscardor_metodo_2_altas').attr('required', true);
        }
    });

    $('#Agregar_manualmente_editar_check_2').change(function() {
        if (this.checked) {
            $('#Manualmente_metodo_2').show();
            $('#Ventana_api_direcciones_modulo2').hide();
            $('#text_area_metodo_2').attr('required', true);
        } else {
            $('#Manualmente_metodo_2').hide();
            $('#Ventana_api_direcciones_modulo2').show();
            $('#text_area_metodo_2').removeAttr('required');
        }
    });


    $('#Metodo2').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // obtiene los datos del formulario
        var Nombre_titular = $('#Nombre_titular_metodo2').val();
        var Apellido_Materno = $('#Apellido_Materno_editar_metodo_2').val();
        var Apellido_Paterno = $('#Apellido_Paterno_editar_metodo_2').val();
        var Telefono_titular = $('#Telefono_titular_metodo_2').val();
        var telefono_casa_metodo_2 = $('#telefono_casa_metodo_2').val();
        var Direccion = $('#Direccion_completa').val();

        if ($('#Agregar_manualmente_editar_check_2').is(':checked')) {
            var Completa_m2_editar = $('#text_area_metodo_2').val();
            var Nr_Interior_2_editar = $('#Nr_Interior_metodo2').val() || 'S/N';
            var Nr_Exterior_m2_editar = $('#Nr_Exterior_metodo_2').val() || 'S/N';
            var calle_av_m2 = $('#calle_metodo2').val();
            // Si el checkbox está marcado, agrega la dirección manual al objeto formData
            Direccion = Completa_m2_editar + ", " + calle_av_m2 + ", No. Interior: " + Nr_Interior_2_editar +
                ", No. Exterior: " + Nr_Exterior_m2_editar;
        } else {
            var Completa_m2_editar_normal = $('#Direccion_completa').val();
            var Nr_Interior_3 = $('#Nr_Interior_metodo2').val() || 'S/N';
            var Nr_Exterior_3 = $('#Nr_Exterior_metodo_2').val() || 'S/N';
            var calle_av_m3 = $('#calle_metodo2').val();
            Direccion = Completa_m2_editar_normal + ", " + calle_av_m3 + ", No. Interior: " + Nr_Interior_3 +
                ", No. Exterior: " +
                Nr_Exterior_3;
        }


        var formData = {
            Nombre_titular: Nombre_titular,
            Apellido_Materno: Apellido_Materno,
            Apellido_Paterno: Apellido_Paterno,
            Telefono_titular: Telefono_titular,
            telefono_casa_metodo_2: telefono_casa_metodo_2,
            Direccion: Direccion
        };

        // alert(JSON.stringify(formData));

        $.ajax({
            url: 'Altas/Nuevo_titular_metodo_2.php', // reemplazar con la URL correcta
            method: 'POST', // método de envío
            data: formData,
            success: function(response) {
                // maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();

                if (response.includes('exito')) {
                    $('#titular_exitoso_metodo_2').hide().show();

                    // Ocultar la alerta después de 5 segundos
                    setTimeout(function() {
                        $('#titular_exitoso_metodo_2').hide();
                    }, 5000);

                    // Recargar la tabla
                    $('#tbl-contact').DataTable().ajax.reload();

                    // Restablecer el formulario
                    $('#Metodo2')[0].reset();
                } else if (response.includes('error')) {
                    $('#titular_error_metodo_2').hide().show();

                }


            }
        });
    });






































































    // Metodo 1 de edicion de datos 

    // Solo permite persionar uno de los dos
    function deseleccionarOtroCheckbox(checkbox) {
        if (checkbox.id === "checkDireccion") {
            // Si se selecciona "Modificar Dirección", deselecciona "Agregar Manualmente"
            document.getElementById("Agregar_manualmente_editar_check").checked = false;
        } else if (checkbox.id === "Agregar_manualmente_editar_check") {
            // Si se selecciona "Agregar Manualmente", deselecciona "Modificar Dirección"
            document.getElementById("checkDireccion").checked = false;
        }
    }
    // Formulario de edicion con sus validaciones 
    $('#Datos_calle_editar_check').hide()
    $(document).ready(function() {
        $('#checkDireccion').change(function() {
            if (this.checked) {
                $('#oculto').show();
                $('#text_area').hide();
                $('#Datos_calle_editar_check').show();
                $('#conten_check').show();
                $('#titular_edicion_exitoso').hide();
                $('#Estado_editar, #Municipio_editar, #Colonia_editar, #calle_editar_bien')
                    .attr('required', true);
            } else {
                $('#oculto').hide();
                $('#Datos_calle_editar_check').hide();
                $('#conten_check').hide();
                $('#text_area').hide();
                $('#titular_edicion_exitoso').hide();
                $('#Estado_editar, #Municipio_editar, #Colonia_editar, #calle_editar_bien, #Agregar_manualmente_editar_metodo_1')
                    .removeAttr('required');
            }

            $('#Agregar_manualmente_editar_check').change(function() {
                if (this.checked) {
                    $('#text_area').show();
                    $('#oculto').hide();
                    $('#Estado_editar, #Municipio_editar, #Colonia_editar')
                        .attr('required', false);
                    $('#Agregar_manualmente_editar_metodo_1').attr('required', true);
                } else {
                    $('#oculto').show();
                    $('#text_area').hide();
                    $('#oculto').hide();
                    $('#Datos_calle_editar_check').hide();
                    $('#Estado_editar, #Municipio_editar, #Colonia_editar, #Agregar_manualmente_editar_metodo_1')
                        .removeAttr('required');
                }
            });

        });




        $(document).ready(function() {
            $('#Editar_titular').on('submit', function(event) {
                event.preventDefault();

                var id_titular_editar = $('#Identificador_metodo_1').val();
                var Nombre_titular_editar = $('#Nombre_titular_editar_metodo_1').val();
                var Apellido_Paterno_editar = $('#Apellido_Paterno_editar_metodo_1').val();
                var Apellido_Materno_Editar = $('#Apellido_Materno_editar_metodo_1').val();
                var Telefono_titular_editar = $('#telefono_editar_metodo_1').val();
                var telefono_casa_edita = $('#telefono_casa_metodo_1').val();
                var Direccion = $('#Direccion_editar_metodo_1').val();

                if ($('#checkDireccion').is(':checked')) {
                    // Construir la dirección basada en la dirección predeterminada
                    var EstadoNombre = $('#EstadoNombre_editar').val();
                    var MunicipioNombre = $('#MunicipioNombre_editar').val();
                    var calle_av = $('#calle_editar_bien').val();
                    var ColoniaNombre = $('#ColoniaNombre_editar').val();
                    var CodigoPostal = $('#CodigoPostal_editar').val();
                    var Nr_Interior_editar = $('#Nr_Interior_editar').val() || 'S/N';
                    var Nr_Exterior_editar = $('#Nr_Exterior_editar').val() || 'S/N';
                    Direccion = EstadoNombre + ", " + MunicipioNombre + ", " + ColoniaNombre +
                        ", CP: " +
                        CodigoPostal +
                        ", " + calle_av + ", No. Interior: " + Nr_Interior_editar +
                        ", No. Exterior: " +
                        Nr_Exterior_editar;
                } else if ($('#Agregar_manualmente_editar_check').is(':checked')) {
                    // Construir la dirección basada en la selección del otro select
                    var Agregar_manualmente_editar_metodo_1 = $('#Agregar_manualmente_editar_metodo_1').val();
                    var calle_av = $('#calle_editar_bien').val();
                    var Nr_Interior_editar = $('#Nr_Interior_editar').val() || 'S/N';
                    var Nr_Exterior_editar = $('#Nr_Exterior_editar').val() || 'S/N';
                    Direccion = Agregar_manualmente_editar_metodo_1 + " " + calle_av + ", No. Interior: " + Nr_Interior_editar +
                        ", No. Exterior: " +
                        Nr_Exterior_editar;
                }

                var formData = {
                    id_titular_editar: id_titular_editar,
                    Nombre_titular_editar: Nombre_titular_editar,
                    Apellido_Paterno_editar: Apellido_Paterno_editar,
                    Apellido_Materno_Editar: Apellido_Materno_Editar,
                    checkDireccion: $('#checkDireccion').is(':checked') ? 1 : 0, // Envía 1 si está marcado, 0 si no
                    Direccion: Direccion,
                    Telefono_titular_editar: Telefono_titular_editar,
                    telefono_casa_edita: telefono_casa_edita
                };

                // alert(JSON.stringify(formData));

                $.ajax({
                    url: 'Editar/Editar_titular.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);

                        $('.alert').hide(); // Ocultar todas las alertas
                        if (response.includes('exito')) {
                            $('#titular_edicion_exitoso').hide().show();
                            // Recargar la tabla
                            $('#tbl-contact').DataTable().ajax.reload();
                            $('#Editar_titular')[0].reset();
                            $('#guardar_metodo_1').hide();

                            // Resto del código...
                        } else if (response.includes('error')) {
                            $('#titular_edicion_error').hide().show();

                            // Recargar la tabla
                            $('#tbl-contact').DataTable().ajax.reload();
                        }
                    }
                });
            });
        });




    });


    // METODO 1
    $('#Estado, #Municipio, #Colonia').attr('required', true);
    $('#Ingresar_Manualmente').change(function() {
        if (this.checked) {
            $('#Contenedor_Direccion_manual').show();
            $('#Direccion_titular_metodo_1').hide();
            $('#Direccion_manual').attr('required', true);
            $('#Estado, #Municipio, #Colonia').attr('required', false);
        } else {
            $('#Contenedor_Direccion_manual').hide();
            $('#Direccion_titular_metodo_1').show();
            $('#Direccion_manual').removeAttr('required');
            $('#Estado, #Municipio, #Colonia').attr('required', true);
        }
    });

    // Mandamos los datos teniendo en cuenta las peticiones ajax
    $(document).ready(function() {
        $('#Nuevo_titular').on('submit', function(event) {
            event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

            // obtiene los datos del formulario
            var Nombre_titular = $('#Nombre_titular').val();
            var Apellido_Paterno = $('#Apellido_Paterno').val();
            var Apellido_Materno = $('#Apellido_Materno').val();
            var Telefono_titular = $('#Telefono_titular').val();
            var Telefono_casa = $('#Telefono_casa').val();
            var Direccion = '';
            // Verificar si el checkbox "Ingresar_Manualmente" está marcado
            // Verificar si el checkbox "Ingresar_Manualmente" está marcado
            if ($('#Ingresar_Manualmente').is(':checked')) {
                // Usar dirección manual
                var Manual = $('#Direccion_manual').val();
                var calle_av = $('#calle').val();
                var Nr_Interior = $('#Nr_Interior').val() || 'S/N';
                var Nr_Exterior = $('#Nr_Exterior').val() || 'S/N';
                Direccion = Manual + " " + calle_av + ", No. Interior: " + Nr_Interior +
                    ", No. Exterior: " + Nr_Exterior;
            } else {
                var EstadoNombre = $('#EstadoNombre').val();
                var MunicipioNombre = $('#MunicipioNombre').val();
                var calle_av = $('#calle').val();
                var ColoniaNombre = $('#ColoniaNombre').val();
                var CodigoPostal = $('#CodigoPostal').val();
                var Nr_Interior = $('#Nr_Interior').val() || 'S/N';
                var Nr_Exterior = $('#Nr_Exterior').val() || 'S/N';
                Direccion = EstadoNombre + ", " + MunicipioNombre + ", " + ColoniaNombre +
                    ", CP: " + CodigoPostal +
                    ", " + calle_av + ", No. Interior: " + Nr_Interior + ", No. Exterior: " + Nr_Exterior;
            }


            var formData = {
                Nombre_titular: Nombre_titular,
                Apellido_Paterno: Apellido_Paterno,
                Apellido_Materno: Apellido_Materno,
                Telefono_titular: Telefono_titular,
                Telefono_casa: Telefono_casa,
                Direccion: Direccion
            };

            // alert(JSON.stringify(formData));


            $.ajax({
                url: 'Altas/Nuevo_titular.php', // reemplazar con la URL correcta
                method: 'POST', // método de envío
                data: formData,
                success: function(response) {
                    // maneja la respuesta del servidor después de enviar los datos del formulario
                    console.log(response);
                    if (response.includes('exito')) {
                        $('#titular_exitoso').hide().show();

                        // Ocultar la alerta después de 5 segundos
                        setTimeout(function() {
                            $('#titular_exitoso').hide();

                        }, 5000);

                        // Recargar la tabla
                        $('#tbl-contact').DataTable().ajax.reload();

                        // Restablecer el formulario
                        $('#Nuevo_titular')[0].reset();
                    } else if (response.includes('error')) {
                        $('#titular_error').hide().show();
                        $('#titular_exitoso').hide();

                    }
                }
            });
        });
    });




























    // Este codigo es el encargado de realizar todas las peticiones a la api para poder llevar un orden
    function titleCase(str) {
        return str.toLowerCase().split(' ').map(function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        }).join(' ');
    }

    function setButtonDataTarget(buttonClass, targetValue) {
        var button = document.querySelector(buttonClass);
        if (button) {
            button.setAttribute('data-target', targetValue);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        var estadoSelect = document.getElementById('Estado');
        var municipioSelect = document.getElementById('Municipio');
        var coloniaSelect = document.getElementById('Colonia');

        function handleAPIError(response) {
            if (!response.ok) {
                throw new Error('Error ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        }


        fetch('api/obtener_estados.php')
            .then(handleAPIError)
            .then(data => {
                if (data && data.estados && Array.isArray(data.estados)) {
                    data.estados.forEach(estado => {
                        var nombreEstado = titleCase(estado.ESTADO);
                        var option = document.createElement('option');
                        option.value = estado.ESTADO_ID;
                        option.setAttribute('data-name', nombreEstado);
                        option.textContent = nombreEstado;
                        estadoSelect.appendChild(option);
                    });
                } else {
                    $('#metodo_2_modal_editar').show();
                    $('#metodo_1_modal_editar').hide();
                    setButtonDataTarget('.continue-application', '#nuevo_titular_metodo_2');
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });

        estadoSelect.addEventListener('change', function() {
            var estadoId = this.value;
            fetch('api/obtener_municipios.php?estado_id=' + estadoId)
                .then(handleAPIError)
                .then(data => {
                    municipioSelect.innerHTML =
                        '<option value="">Selecciona un municipio</option>';
                    data.municipios.forEach(municipio => {
                        var nombreMunicipio = titleCase(municipio.MUNICIPIO);
                        var option = document.createElement('option');
                        option.value = municipio.MUNICIPIO_ID;
                        option.setAttribute('data-name', nombreMunicipio);
                        option.textContent = nombreMunicipio;
                        municipioSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        });

        municipioSelect.addEventListener('change', function() {
            var estadoId = estadoSelect.value;
            var municipioId = this.value;
            if (municipioId) {
                fetch('api/obtener_colonias.php?id_estado=' + estadoId + '&id_mun=' +
                        municipioId)
                    .then(handleAPIError)
                    .then(data => {
                        if (data.error) {
                            console.error(data.message);
                        } else {
                            coloniaSelect.innerHTML =
                                '<option value="">Selecciona una colonia</option>';
                            data.colonias.forEach(colonia => {
                                var option = document.createElement('option');
                                option.value = colonia.COLONIA_ID;
                                option.setAttribute('data-name', colonia.COLONIA);
                                option.setAttribute('data-cp', colonia.CP);
                                option.textContent = colonia.COLONIA + " (CP: " +
                                    colonia.CP +
                                    ")";
                                coloniaSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:',
                            error);
                    });
            }
        });

        estadoSelect.addEventListener('change', function() {
            var estadoNombre = this.options[this.selectedIndex].getAttribute('data-name');
            document.getElementById('EstadoNombre').value = estadoNombre;
        });

        municipioSelect.addEventListener('change', function() {
            var municipioNombre = this.options[this.selectedIndex].getAttribute('data-name');
            document.getElementById('MunicipioNombre').value = municipioNombre;
        });

        coloniaSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var coloniaNombre = selectedOption.getAttribute('data-name');
            var codigoPostal = selectedOption.getAttribute('data-cp');
            document.getElementById('ColoniaNombre').value = coloniaNombre;
            document.getElementById('CodigoPostal').value = codigoPostal;
        });
    });



    document.addEventListener('DOMContentLoaded', function() {
        var estadoSelect = document.getElementById('Estado_editar');
        var municipioSelect = document.getElementById('Municipio_editar');
        var coloniaSelect = document.getElementById('Colonia_editar');

        fetch('api/obtener_estados.php')
            .then(response => response.json())
            .then(data => {
                data.estados.forEach(estado => {
                    var nombreEstado = titleCase(estado.ESTADO);
                    var option = document.createElement('option');
                    option.value = estado.ESTADO_ID;
                    option.setAttribute('data-name', nombreEstado);
                    option.textContent = nombreEstado;
                    estadoSelect.appendChild(option);
                });
            });

        // Cargar los municipios cuando se selecciona un estado
        estadoSelect.addEventListener('change', function() {
            var estadoId = this.value;
            fetch('api/obtener_municipios.php?estado_id=' + estadoId)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML =
                        '<option value="">Selecciona un municipio</option>';
                    data.municipios.forEach(municipio => {
                        var nombreMunicipio = titleCase(municipio.MUNICIPIO);
                        var option = document.createElement('option');
                        option.value = municipio.MUNICIPIO_ID;
                        option.setAttribute('data-name', nombreMunicipio);
                        option.textContent = nombreMunicipio;
                        municipioSelect.appendChild(option);
                    });
                });
        });



        municipioSelect.addEventListener('change', function() {
            var estadoId = estadoSelect.value;
            var municipioId = this.value;
            if (municipioId) {
                fetch('api/obtener_colonias.php?id_estado=' + estadoId + '&id_mun=' +
                        municipioId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.message);
                        } else {
                            coloniaSelect.innerHTML =
                                '<option value="">Selecciona una colonia</option>';
                            data.colonias.forEach(colonia => {
                                var option = document.createElement('option');
                                option.value = colonia.COLONIA_ID;
                                option.setAttribute('data-name', colonia.COLONIA);
                                option.setAttribute('data-cp', colonia
                                    .CP
                                ); // Agregar el código postal como un atributo de datos
                                option.textContent = colonia.COLONIA + " (CP: " +
                                    colonia.CP + ")";
                                coloniaSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:',
                            error);
                    });
            }
        });

        // Evento 'change' para el selector de estado
        estadoSelect.addEventListener('change', function() {
            var estadoNombre = this.options[this.selectedIndex].getAttribute('data-name');
            document.getElementById('EstadoNombre_editar').value = estadoNombre;
        });

        // Evento 'change' para el selector de municipio
        municipioSelect.addEventListener('change', function() {
            var municipioNombre = this.options[this.selectedIndex].getAttribute('data-name');
            document.getElementById('MunicipioNombre_editar').value = municipioNombre;
        });

        // Evento 'change' para el selector de colonia
        coloniaSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var coloniaNombre = selectedOption.getAttribute('data-name');
            var codigoPostal = selectedOption.getAttribute('data-cp');

            document.getElementById('ColoniaNombre_editar').value = coloniaNombre;
            document.getElementById('CodigoPostal_editar').value = codigoPostal;
        });
    });
</script>