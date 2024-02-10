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

// Consulta SQL
$query = "SELECT u.Usuario,
COALESCE(tit.Cantidad, 0) +
COALESCE(pan.Cantidad, 0) +
COALESCE(tum.Cantidad, 0) +
COALESCE(ser.Cantidad, 0) +
COALESCE(a.Cantidad, 0) +
COALESCE(fina.Cantidad, 0) +
COALESCE(pago.Cantidad, 0) +
COALESCE(pert.Cantidad, 0) +
COALESCE(histu.Cantidad, 0) +
COALESCE(lote.Cantidad, 0) +
COALESCE(titum.Cantidad, 0) +
COALESCE(pa.Cantidad, 0) AS CantidadTotalActividades
FROM mxpt_usuarios u
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM mxpt_finados
GROUP BY Responsable_cap
) tit ON u.id_usuario = tit.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM mxpt_panteones
GROUP BY Responsable_cap
) pan ON u.id_usuario = pan.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap_tum, COUNT(*) AS Cantidad
FROM mxpt_tumbas
GROUP BY Responsable_cap_tum
) tum ON u.id_usuario = tum.Responsable_cap_tum
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM mxpt_servicios
GROUP BY Responsable_cap
) ser ON u.id_usuario = ser.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM mxpt_titulares
GROUP BY Responsable_cap
) a ON u.id_usuario = a.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM catalogo_historico_finados
GROUP BY Responsable_cap
) fina ON u.id_usuario = fina.Responsable_cap
LEFT JOIN (
SELECT Responsable_Cambio, COUNT(*) AS Cantidad
FROM catalogo_historico_pagos
GROUP BY Responsable_Cambio
) pago ON u.id_usuario = pago.Responsable_Cambio
LEFT JOIN (
SELECT Responsable_cambio, COUNT(*) AS Cantidad
FROM catalogo_historico_servicios
GROUP BY Responsable_cambio
) pert ON u.id_usuario = pert.Responsable_cambio
LEFT JOIN (
SELECT Responsable_Cambio, COUNT(*) AS Cantidad
FROM catalogo_historico_titulares
GROUP BY Responsable_Cambio
) histu ON u.id_usuario = histu.Responsable_Cambio
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM catalogo_lotes
GROUP BY Responsable_cap
) lote ON u.id_usuario = lote.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM catalogo_tipo_tumba
GROUP BY Responsable_cap
) titum ON u.id_usuario = titum.Responsable_cap
LEFT JOIN (
SELECT Responsable_cap, COUNT(*) AS Cantidad
FROM mxpt_pagos
GROUP BY Responsable_cap
) pa ON u.id_usuario = pa.Responsable_cap
ORDER BY CantidadTotalActividades DESC";

// Ejecutar la consulta
$result = $mysqli->query($query);

// Verificar si hubo un error en la consulta
if (!$result) {
    die('Error en la consulta: ' . mysqli_error($mysqli));
}
$usuarios = [];
$actividades = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row['Usuario'];
    $actividades[] = $row['CantidadTotalActividades']; // Cambiamos 'CantidadActividades' a 'CantidadTotalActividades'
}

// Codificar los datos en JSON para su uso en JavaScript
$usuariosJSON = json_encode($usuarios);
$actividadesJSON = json_encode($actividades);


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
    <meta charset="UTF-8">

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
    <script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="../js/validaciones_input.js"></script>
</head>
<style>
    .alert {
        position: relative !important;
        /* margin: 0px 10px 30px 10px !important; */
        animation: pulse 1.2s !important;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        10% {
            transform: scale(1.05);
        }

        60% {
            transform: scale(1);
        }
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

                        <div class="col-md-7">
                            <!-- Tercer contenedor -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="tercer-contenedor" class="ct-chart">



                                        <div class="table-responsive">
                                            <table id="Usuarios" class="display nowrap table-striped table-bordered table-sm table-responsive-sm " style="width:100%;">
                                                <caption class="caption table-bordered table-striped">
                                                    Control Usuarios</caption>
                                                <thead>
                                                    <tr class="thead-tablas">
                                                        <th>No.</th>
                                                        <th>Nombre Completo</th>
                                                        <th>Tipo Usuario</th>
                                                        <th>Estado</th>
                                                        <th>Usuario</th>
                                                        <th>Editar</th>
                                                        <th>Fecha Captura</th>
                                                        <th>Correo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sqsl = $mysqli->query("SELECT * FROM mxpt_usuarios WHERE Estado = 'Activo' AND Tipo_Usuario != 'Tecnosolucionext'");

                                                    while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                                                        <tr>
                                                            <td><?php echo $row['id_usuario']; ?></td>
                                                            <td><?php echo $row['Nombre'] . " " . $row['Apellidos']; ?></td>
                                                            <td><?php echo $row['Tipo_Usuario']; ?></td>
                                                            <td class="<?php echo $row['Estado'] == 'Activo' ? 'estado-activo' : 'estado-Inactivo'; ?>">
                                                                <?php echo $row['Estado'] == 'Activo' ? '🟢' : '🔴'; ?>
                                                            </td>

                                                            <td>
                                                                <div class="User_Resp">
                                                                    <?php echo $row['Usuario']; ?></div>
                                                            </td>
                                                            <td>
                                                                <button class="btn editbtn" data-toggle="modal" data-target="#Editar_Usuario" data-id_usuario="<?php echo htmlspecialchars($row['id_usuario']); ?>" data-Nombre="<?php echo htmlspecialchars($row['Nombre']); ?>" data-adsdasd="<?php echo htmlspecialchars($row['Tipo_Usuario']); ?>" data-josedusi="<?php echo htmlspecialchars($row['Apellidos']); ?>" data-Estado="<?php echo htmlspecialchars($row['Estado']); ?>" data-Usuario="<?php echo htmlspecialchars($row['Usuario']); ?>" data-gmail="<?php echo htmlspecialchars($row['Mail']); ?>">
                                                                    Editar
                                                                </button>

                                                            </td>


                                                            <td style=""><?php echo $row['Mail']; ?></td>
                                                            <td>

                                                                <div class='date'> <input type='date' class='form-control date-input-view-only' value='<?php echo $row['Fecha_Captura_Usuario']; ?>' readonly><i class='fa-regular fa-calendar caler'></i>
                                                                </div>
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

                        <div class="col-md-5">
                            <!-- Cuarto contenedor (en la parte inferior) -->
                            <div class="panel">
                                <div class="panel-body">
                                    <div id="cuarto-contenedor" class="ct-chart">
                                        <canvas id="usuariosChart" width="350" height="450px"></canvas>
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


<div class="modal fade bs-example-modal-lg" id="Usuarios_Inactivos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Usuarios inactivos</h4>
            </div>
            <div class="modal-body">

                <table id="Usuarios_inact" class="display nowrap table-striped table-bordered table-sm table-responsive-sm " style="width:100%;">
                    <caption class="caption table-bordered table-striped">
                        Control Usuarios</caption>
                    <thead>
                        <tr class="thead-tablas">
                            <th>No.</th>
                            <th>Nombre Completo</th>
                            <th>Tipo Usuario</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Fecha Captura</th>
                            <th>Fecha de Baja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqsl = $mysqli->query("SELECT * FROM mxpt_usuarios WHERE Estado = 'Inactivo'");
                        while ($row = $sqsl->fetch_array(MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><?php echo $row['id_usuario']; ?></td>
                                <td><?php echo $row['Nombre'] . " " . $row['Apellidos']; ?></td>
                                <td><?php echo $row['Tipo_Usuario']; ?></td>
                                <td><?php echo $row['Estado']; ?> </td>
                                <td>
                                    <div class=" User_Resp"><?php echo $row['Usuario']; ?></div>
                                </td>
                                <td><?php echo $row['Mail']; ?></td>
                                <td><?php echo $row['Fecha_Captura_Usuario']; ?></td>
                                <td><?php echo $row['Fecha_Baja_Usuario']; ?></td>

                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>



</html>
<div class="modal fade bs-example-modal-sm" id="Nuevo_usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="formulario" novalidate>
                    <div id="grupo__name">
                        <label for="name" class="formulario__label">Nombre</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="name" id="name" relect>
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">No debe contener números, solo letras.</p>
                    </div>

                    <div id="grupo__Apellido">
                        <label for="Apellido" class="formulario__label">Apellidos</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="Apellido" id="Apellido">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">No debe contener números, solo letras.</p>
                    </div>
                    <br>
                    <div id="grupo__Tipo_Usuario">
                        <label for="Tipo_Usuario">Seleccione Tipo Usuario</label>
                        <select class="form-control" name="Tipo_Usuario" id="Tipo_Usuario">
                            <option value="Administrador Sistemas" Select>Administrador Sistemas</option>
                            <option value="Administrador Panteones">Administrador Panteones</option>
                            <option value="Tesoreria">Administrador Tesoreria</option>
                        </select>
                    </div>

                    <div id="grupo__Usuario">
                        <label for="Usuario" class="formulario__label">Usuario</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="Usuario" id="Usuario">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">Debe contener al menos una letra mayúscula y al menos un
                            número, además, su longitud debe estar comprendida entre 5 y 10 caracteres.</p>
                    </div>
                    <div id="grupo__pass1">
                        <label for="pass1" class="formulario__label">Contraseña</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="pass1" id="pass1">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">La contraseña debe tener como máximo 8 caracteres, incluyendo
                            al menos una letra mayúscula, una minúscula, un número y un símbolo seguro.</p>
                    </div>

                    <div id="grupo__correo">
                        <label for="correo" class="formulario__label">Correo</label>
                        <div class="formulario__grupo-input">
                            <input type="email" required class="formulario__input" name="correo" id="correo">
                            <i class="formulario__validacion-estado fas fa-times-circle" style="    top: 58px;"></i>
                        </div>
                    </div>
                    <div class="alert alert-verde" style="display:none;" id="formulario__mensaje-exito">
                        <strong>¡Éxito!</strong> ¡Se ha agregado su usuario correctamente!
                    </div>
                    <div class="alert alert-warning" style="display:none;" id="Rellene_campos">
                        <strong>¡Error!</strong> Por favor rellene los campos correctamente.
                    </div>
                    <div class="alert alert-warning" style="display:none;" id="El_usuario_existe">
                        <strong>¡Error!</strong> El Usuario ya existe
                    </div>

                    <div class="alert alert-warning" style="display:none;" id="El_correo_existe">
                        <strong>¡Error!</strong> El Correo ya esta en uso
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="formulario__mensaje-error">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-warning" id="guardar_nuevo_usuario">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" id="Editar_Usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Editar Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="formulario_editar">
                    <input type="hidden" name="id_usuario_editar" id="id_usuario_editar">

                    <div id="grupo__name_2">
                        <label for="name" class="formulario__label">Nombre</label>
                        <div class="formulario__grupo-input">
                            <input type="text" class="formulario__input" autofocus="autofocus" s name="name_2" id="name_2">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">No debe contener números, solo letras.</p>
                    </div>

                    <div id="grupo__Apellido_2">
                        <label for="Apellido" class="formulario__label">Apellidos</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="Apellido_2" id="Apellido_2">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">No debe contener números, solo letras.</p>
                    </div>
                    <br>
                    <div id="grupo__Tipo_Usuario_2">
                        <label for="Tipo_Usuario">Seleccione Tipo Usuario</label>
                        <select class="form-control" name="Tipo_Usuario_2" id="Tipo_Usuario_2">
                            <option value="Administrador Sistemas">Administrador Sistemas</option>
                            <option value="Administrador Panteones">Administrador Panteones</option>
                            <option value="Tesoreria">Administrador Tesoreria</option>
                        </select>
                    </div><br>
                    <div id="grupo__estado_2">
                        <label for="Tipo_Usuario">Estado</label>
                        <select class="form-control" name="estado_editar" id="estado_editar">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div id="grupo__Usuario_2">
                        <label for="Usuario" class="formulario__label">Usuario</label>
                        <div class="formulario__grupo-input">
                            <input type="text" required class="formulario__input" name="Usuario_2" id="Usuario_2">
                            <i class="formulario__validacion-estado fas fa-times-circle"></i>
                        </div>
                        <p class="formulario__input-error">Debe contener al menos una letra mayúscula y al menos un
                            número, además, su longitud debe estar comprendida entre 5 y 10 caracteres.</p>
                    </div>



                    <div id="grupo__correo_2">
                        <label for="correo" class="formulario__label">Correo</label>
                        <div class="formulario__grupo-input">
                            <input type="email" required class="formulario__input" name="correo_2" id="correo_2">
                            <i class="formulario__validacion-estado fas fa-times-circle" style="top: 58px;"></i>
                        </div>
                    </div>

                    <div class="checkbox" data-dismiss="modal" data-toggle="modal" data-target="#cambiar_contra">
                        <label>
                            <input type="checkbox"> Cambiar Contraseña
                        </label>
                    </div>

                    <div class="alert alert-verde" style="display:none;" id="formulario__mensaje-exito-2">
                        <strong>¡Éxito!</strong> ¡Se ha agregado su usuario correctamente!
                    </div>
                    <div class="alert alert-warning" style="display:none;" id="Rellene_campos-2">
                        <strong>¡Error!</strong> Por favor rellene los campos correctamente.
                    </div>
                    <div class="alert alert-danger" style="display:none;" id="formulario__mensaje-error-2">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>
                    <div class="alert alert-warning" style="display:none;" id="El_usuario_existe">
                        <strong>¡Error!</strong> El Usuario ya existe
                    </div>

                    <div class="alert alert-warning" style="display:none;" id="El_correo_existe">
                        <strong>¡Error!</strong> El Correo ya esta en uso
                    </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
                <button type="sutmid" class="btn btn-warning">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="cambiar_contra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Cambiar Contraseña</h4>
            </div>
            <div class="modal-body">
                <form id="formulario_cambio_contra" autocomplete="off">
                    <input type="hidden" id="id_usuario_editar_3">
                    <div class="alert alert-info" style="display: block !important;" id="Contraseña_temporal">
                        <strong>¡Aviso!</strong> Se generará una contraseña temporal. Una vez que el usuario la ingrese,
                        podrá crear sus credenciales.

                    </div>


                    <div class="form-group">
                        <div class="input-group">

                            <input type="text" class="form-control" id="Contenedor_contraseña" onkeydown="return false;" required>
                            <div class="input-group-addon" style="cursor:pointer;" onclick="copiarContrasena()" id="btnCopiar">
                                <i class="fa-regular fa-copy"></i> Copiar
                            </div>

                        </div>
                    </div>

                    <div class="alert alert-verde" style="display:none;" id="Temporal_bien">
                        <strong>¡Éxito!</strong> ¡Se ha agregado su usuario correctamente!
                    </div>

                    <div class="alert alert-danger" style="display:none;" id="Temporal_error">
                        <strong>¡Error!</strong> Por favor, inténtalo de nuevo. Si el problema persiste, considera las siguientes acciones:
                        - Verifica tu conexión a internet.
                        - Actualiza la página o la aplicación.
                        - Cierra sesión y vuelve a iniciar sesión.
                        Si el problema continúa, contacta al soporte técnico para recibir ayuda.
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
                <button type="button" class="btn btn-success" onclick="generarContrasenaTemporal()">Generar</button>
                <button type="sutmid" class="btn btn-warning">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="aviso_privacidad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Aviso⚠️</h4>
            </div>
            <div class="modal-body">

                <center> <strong>¡Atención!</strong> Cerrar para aplicar los cambios.</center>

            </div>
            <div class="modal-footer">
                <a type="button" href="cerrar_sesion/cerrar_sesion.php" class="btn btn-primary">Aceptar</a>
            </div>
        </div>
    </div>
</div>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://kit.fontawesome.com/cde87c5826.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>






<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $('#formulario_cambio_contra').on('submit', function(event) {
        event.preventDefault(); // evita que el formulario se envíe de forma predeterminada

        // Obtiene los datos del formulario
        var usuario = $('#id_usuario_editar_3').val();
        var Temporal = $('#Contenedor_contraseña').val();
        // Crea un objeto con los datos del formulario
        var formData = {
            Temporal: Temporal,
            usuario: usuario
        };
        // alert(JSON.stringify(formData));
        $.ajax({
            url: 'Editar/Editar_Contraseña.php', // Reemplaza con la URL correcta
            method: 'POST', // Método de envío
            data: formData,
            success: function(response) {
                // Maneja la respuesta del servidor después de enviar los datos del formulario
                console.log(response);
                $('.alert').hide();
                if (response == 'Exito') {
                    $('#Temporal_bien').hide().addClass('alert').show();
                    $('#cambiar_contra').modal('hide'); // Oculta el modal "cambiar_contra"
                } else {
                    $('#Temporal_error').hide().addClass('alert').show();
                }
            }
        });
    });

    $('.modal').on('hidden.bs.modal', function() {
        $('.alert').hide();
        $('#Contraseña_temporal').show();
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




    function generarContrasenaTemporal() {
        var longitud = 9; // Longitud de la contraseña
        var caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#$@*'; // Caracteres permitidos
        var contrasena = '';

        for (var i = 0; i < longitud; i++) {
            var caracterAleatorio = caracteres.charAt(Math.floor(Math.random() * caracteres.length));
            contrasena += caracterAleatorio;
        }

        var contenedorContraseña = document.querySelector('#Contenedor_contraseña');
        contenedorContraseña.value = contrasena;

    }

    function copiarContrasena() {
        var contenedorContraseña = document.getElementById('Contenedor_contraseña');
        contenedorContraseña.select();
        document.execCommand('copy');
        alert('Contraseña copiada al portapapeles: ' + contenedorContraseña.value);
    }




    const inputContraseña = document.querySelector('#Contenedor_contraseña');
    inputContraseña.addEventListener('input', function(e) {
        e.preventDefault();
        return false;
    });







    $('#Editar_Usuario').on('shown.bs.modal', function() {
        $('#name_2').focus();
        $('#Apellido_2').focus();
        $('#Usuario_2').focus();
        $('#correo_2').focus();
    });
    $(document).on('click', '.editbtn', function() {
        // Obtener los datos de los atributos de datos
        var id_usuario = $(this).data('id_usuario');
        var id_cambiar_contra = $(this).data('id_usuario');
        var nombre = $(this).data('nombre');
        var Apellidos = $(this).data('josedusi');
        var estado = $(this).data('estado');
        var Tipo_Usuario = $(this).data('adsdasd');
        var usuario = $(this).data('usuario');
        var gmail = $(this).data('gmail');

        // Establecer los valores en el formulario de edición
        $('#id_usuario_editar').val(id_usuario);
        $('#id_usuario_editar_3').val(id_cambiar_contra);
        $('#name_2').val(nombre);
        $('#Tipo_Usuario_2').val(Tipo_Usuario);
        $('#Apellido_2').val(Apellidos);
        $('#estado_editar').val(estado);
        $('#Usuario_2').val(usuario);
        $('#correo_2').val(gmail);
    });

    const formulario_2 = document.getElementById('formulario_editar');
    const inputs_2 = document.querySelectorAll('#formulario_editar input');
    const expresiones_2 = {
        name_2: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
        Apellido_2: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
        Usuario_2: /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*#?&]{5,10}$/,
    };

    const campos_2 = {
        name_2: false,
        Apellido_2: false,
        Usuario_2: false,
    };

    const validarFormulario_2 = (e) => {
        switch (e.target.name) {
            case "name_2":
                validarCampo_2(expresiones_2.name_2, e.target, 'name_2');
                break;
            case "Apellido_2":
                validarCampo_2(expresiones_2.Apellido_2, e.target, 'Apellido_2');
                break;
            case "Usuario_2":
                validarCampo_2(expresiones_2.Usuario_2, e.target, 'Usuario_2');
                break;
        }
    };

    const validarCampo_2 = (expresion, input, campo) => {
        if (expresion.test(input.value)) {
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-check-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-times-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.remove(
                'formulario__input-error-activo');
            campos_2[campo] = true;
        } else {
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos_2[campo] = false;
        }
    };

    inputs_2.forEach((input) => {
        input.addEventListener('keyup', validarFormulario_2);
        input.addEventListener('blur', validarFormulario_2);
    });
    $('#formulario_editar').on('submit', (e) => {
        e.preventDefault();
        $('.alert').hide();

        if (campos_2.name_2 && campos_2.Apellido_2 && campos_2.Usuario_2) {
            // Obtener todos los valores del formulario
            var formData = $('#formulario_editar').serializeArray();
            formData.push({
                name: 'id_usuario',
                value: $('#id_usuario_editar').val()
            }); // Agregar el valor del campo oculto
            // alert(JSON.stringify(formData));
            $.ajax({
                url: 'Editar/Editar_Usuario.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('.alert').hide();
                    console.log(response);
                    if (response == 'exito') {
                        $('#formulario__mensaje-exito-2').hide().addClass('alert').show();
                        $('#formulario_editar')[0].reset();
                        $('#aviso_privacidad').modal('show');
                        $('#Editar_Usuario').modal('hide');

                    } else if (response == 'El_usuario_existe') {
                        $('#El_usuario_existe').hide().addClass('alert').show();
                    } else if (response == 'El_correo_existe') {
                        $('#El_correo_existe').hide().addClass('alert').show();
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
            $('#formulario__mensaje').hide().addClass('alert').show();
        }
    });




    const formulario = document.getElementById('formulario');
    const inputs = document.querySelectorAll('#formulario input');
    const expresiones = {
        name: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
        Apellido: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
        Usuario: /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*#?&]{5,10}$/,
        pass1: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{1,8}$/,
    }



    const campos = {
        name: false,
        Apellido: false,
        Usuario: false,
        pass1: false,
    }

    const validarFormulario = (e) => {
        switch (e.target.name) {

            case "name":
                validarCampo(expresiones.name, e.target, 'name');;
                break;
            case "Apellido":
                validarCampo(expresiones.Apellido, e.target, 'Apellido');;
                break;
            case "Usuario":
                validarCampo(expresiones.Usuario, e.target, 'Usuario');;
                break;
            case "pass1":
                validarCampo(expresiones.pass1, e.target, 'pass1');;
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

        } else {
            document.getElementById(`grupo__${campo}`).classList.add('formulario__grupo-incorrecto');
            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-correcto');
            document.querySelector(`#grupo__${campo} i`).classList.add('fa-times-circle');
            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-check-circle');
            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.add(
                'formulario__input-error-activo');
            campos[campo] = false;
        }
    }


    inputs.forEach((input) => {
        input.addEventListener('keyup', validarFormulario);
        input.addEventListener('blur', validarFormulario);
    });


    $('#formulario').on('submit', (e) => {
        e.preventDefault();
        $('.alert').hide();

        if (campos.name && campos.Apellido && campos.Usuario && campos.pass1 && campos.Usuario) {
            // Obtener todos los valores del formulario
            var formData = $('#formulario').serializeArray();


            // alert(JSON.stringify(formData));
            $.ajax({
                url: 'Altas/Nuevo_Usuario.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('.alert').hide();
                    console.log(response);
                    if (response == 'exito') {
                        $('#formulario__mensaje-exito').hide().addClass('alert').show();
                        $('#formulario')[0].reset();
                        $('#guardar_nuevo_usuario').hide(); // Oculta el botón
                        // Elimina iconos y clases de validación en todos los campos
                        inputs.forEach(input => {
                            var campo = input.name;
                            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-incorrecto');
                            document.getElementById(`grupo__${campo}`).classList.remove('formulario__grupo-correcto');
                            document.querySelector(`#grupo__${campo} i`).classList.remove('fa-times-circle');
                            document.querySelector(`#grupo__${campo} .formulario__input-error`).classList.remove('formulario__input-error-activo');
                            campos[campo] = false;
                        });
                    } else if (response == 'El_usuario_existe') {
                        $('#El_usuario_existe').hide().addClass('alert').show();
                    } else if (response == 'El_correo_existe') {
                        $('#El_correo_existe').hide().addClass('alert').show();
                    } else {
                        $('#formulario__mensaje-error').hide().addClass('alert').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error en la solicitud AJAX:', error);
                    console.log('Respuesta completa:', xhr.responseText);
                }
            });
        } else {
            $('#Rellene_campos').hide().addClass('alert').show();
        }
    });

    $(document).ready(function() {
        var tablaUsuariosInact = $('#Usuarios_inact').DataTable({
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
                }
            },

            dom: 'Bfrtip',
            buttons: [{
                extend: 'pdf',
            }]


        });

        $('#Usuarios_Inactivos').on('shown.bs.modal', function(e) {
            tablaUsuariosInact.columns.adjust().draw();
        });

        // Evento que se dispara cuando se cambia el tamaño del modal
        $('#Usuarios_Inactivos').on('resize.bs.modal', function(e) {
            tablaUsuariosInact.columns.adjust().draw();
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
            '<button class="btn btn-default" id="agregarButton"  data-toggle="modal" data-target="#Nuevo_usuario" style="margin: 10px 10px -10px 10px;">Agregar Usuario</button>';
        var deleteButton =
            '<button class="btn btn-primary"data-toggle="modal" data-target="#Usuarios_Inactivos" id="eliminarButton" style="margin: 10px 10px -10px 10px;">Usuarios Inactivos</button>';

        // Insertar los botones en el DOM
        $('#Usuarios_wrapper .dataTables_length').after(addButton + deleteButton);
    });

    // Crear el gráfico de barras para actividades de usuarios
    var ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
    var chartUsuarios = new Chart(ctxUsuarios, {
        type: 'bar',
        data: {
            labels: <?php echo $usuariosJSON; ?>,
            datasets: [{
                label: 'Cantidad de Actividades',
                data: <?php echo $actividadesJSON; ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
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


    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>