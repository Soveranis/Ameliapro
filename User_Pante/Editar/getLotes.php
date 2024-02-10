<?php
include("../../conexiones/conexion.php");

if(isset($_POST['idPanteon'])) {
    $idPanteon = $_POST['idPanteon'];
    $output = '';  // Inicializamos la variable sin contenido

    // Asumiendo que tu tabla catalogo_lotes tiene una columna id_panteon que relaciona cada lote con un panteón
    $lote = mysqli_query($mysqli, "SELECT * FROM catalogo_lotes WHERE id_panteon = $idPanteon");

    while ($da = mysqli_fetch_array($lote)) {
        $output .= '<option value="'.$da['id_lote'].'">'.$da['Lote'].'</option>';
    }

    echo json_encode($output);
}
?>