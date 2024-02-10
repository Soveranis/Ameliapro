$(document).ready(function () {
   // Inicialización del datepicker
   $(".datepicker").datepicker({
      "dateFormat": "yy-mm-dd",
      changeYear: true
   });

   // Inicialización del DataTable
   var dataTable = $('#Tabla_personal').DataTable({
      "language": {
         "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
      },
      searching: false, // Deshabilita la búsqueda predeterminada
      dom: 'Bfrtip', // Define la ubicación de los botones
      buttons: [
         'copy', 'csv', 'excel', 'pdf', 'print' // Opciones de exportación
      ],
      'processing': true, // Muestra un mensaje de procesamiento
      'serverSide': true, // Activa el procesamiento en el servidor
      'serverMethod': 'post',
      'searching': false, // Deshabilita la búsqueda
      'responsive': true,
      'ajax': {
         'url': '../json/server_consultas.php', // URL del servidor para obtener los datos
         'data': function (data) {
            // Lee los valores de fecha
            var buscar_inicio = $('#buscar_inicio').val();
            var buscar_fin = $('#buscar_fin').val();

            // Agrega los valores de fecha a los datos enviados al servidor
            data.buscarFechaInicio = buscar_inicio;
            data.buscarFechaFin = buscar_fin;
         }
      },
      'columns': [
         { data: 'Folio_Panteones' },
         { data: 'Nombre_Servicio' },
         { data: 'Folio_Tesoreria' },
         { data: 'Fecha_acreditacion' },
         { data: 'Nombre_titular' },
         { data: 'Monto_Pago' },
      ],
   });

   // Botón Buscar
   $('#btn_search').click(function () {
      // Realiza una nueva búsqueda y actualiza la tabla
      dataTable.draw();
   });

   // Función para registrar acciones (puede personalizarse)
   function logAction(message) {
      // Envía una solicitud al servidor para registrar la acción
      $.post("log_action.php", { action: message }, function (data) {
         console.log(data); // Puedes eliminar esto si no deseas ver el resultado en la consola
      });
   }
});
