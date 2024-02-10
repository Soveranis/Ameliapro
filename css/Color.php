<?php
// Establecer la cabecera para indicar que el contenido es CSS con codificación UTF-8
header("Content-type: text/css; charset=UTF-8");

// Incluir el archivo de conexión a la base de datos
include('../conexiones/conexion.php');

// Consultar la configuración de colores desde la base de datos
$query_color = "SELECT Color_Pre, Fondo FROM catalogo_configuracion";
$stmt_color = $mysqli->prepare($query_color);
$stmt_color->execute();
$stmt_color->bind_result($Color, $fondo);
$stmt_color->fetch();

// Codificar la imagen de fondo en formato base64
$fondo_pantalla = base64_encode($fondo);
?>

/* Definición de variables CSS personalizadas usando :root */
:root {
    --color-primario: <?php echo $Color; ?>; /* Establecer el color primario */
    --fondo-url: url(data:image/jpeg;base64,<?php echo $fondo_pantalla; ?>); /* Establecer la imagen de fondo codificada en base64 */
}
