</main>

<footer>
    <!-- Footer del sistema -->
</footer>

<!-- Scripts AJAX personalizados - DEBEN IR ANTES DE DATATABLES -->
<script src="<?php echo APP_URL; ?>app/views/js/ajax.js"></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js"></script>

<!-- DataTables Initialization -->
<script>
$(document).ready(function() {
    // Verificar si existe una tabla antes de inicializar DataTables
    if ($('#tablaUsuarios').length > 0) {
        $('#tablaUsuarios').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [5, 6, 7] } // Foto, Editar, Eliminar no ordenables
            ],
            "order": [[0, 'asc']],
            "responsive": true,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    if ($('#tablaCategorias').length > 0) {
        $('#tablaCategorias').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [3, 5, 6] } // Imagen, Editar, Eliminar no ordenables
            ],
            "order": [[0, 'asc']],
            "responsive": true,
            "autoWidth": false
        });
    }

    if ($('#tablaClientes').length > 0) {
        $('#tablaClientes').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [-2, -1] } // Últimas 2 columnas no ordenables
            ],
            "order": [[0, 'asc']],
            "responsive": true,
            "autoWidth": false
        });
    }

    if ($('#tablaProductos').length > 0) {
        $('#tablaProductos').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [1, -2, -1] } // Imagen y últimas 2 no ordenables
            ],
            "order": [[0, 'desc']],
            "responsive": true,
            "autoWidth": false
        });
    }

    // Tabla genérica para otras tablas
    if ($('table.table:not(#tablaUsuarios):not(#tablaCategorias):not(#tablaClientes):not(#tablaProductos)').length > 0) {
        $('table.table:not(#tablaUsuarios):not(#tablaCategorias):not(#tablaClientes):not(#tablaProductos)').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[0, 'asc']],
            "responsive": true,
            "autoWidth": false
        });
    }
});
</script>

</body>
</html>