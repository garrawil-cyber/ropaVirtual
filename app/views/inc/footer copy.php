</main>

<footer>
    <!-- Footer del sistema -->
</footer>

<!-- DataTables Initialization -->
<script>
$(document).ready(function() {
    // Verificar si existe una tabla antes de inicializar DataTables
    if ($('table.table').length > 0) {
        $('table.table').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [4, 5, 6] } // Deshabilitar ordenamiento en columnas de opciones
            ],
            "order": [[0, 'asc']], // Ordenar por la primera columna (#) de forma ascendente
            "responsive": true,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "drawCallback": function(settings) {
                // Reinicializar los formularios AJAX después de cada redibujado
                if (typeof formularios_ajax !== 'undefined') {
                    formularios_ajax(document.querySelectorAll(".FormularioAjax"));
                }
            }
        });
    }
});
</script>

</body>
</html>