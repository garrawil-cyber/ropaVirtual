<?php
// Asegurar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: " . APP_URL . "login/");
    exit();
}

use app\controllers\categoryController;
$insCategoria = new categoryController();

// Incluir head.php
require_once APP_PATH . "app/views/inc/head.php"; 

// Incluir navbar.php usando la constante APP_PATH
$navbar_path = APP_PATH . 'app/views/inc/navbar.php';
	
if (file_exists($navbar_path)) {
    require_once $navbar_path;
} else {
    die("Error: No se pudo cargar el navbar");
}

// Llamar al método sin parámetros de paginación ya que DataTables lo maneja
echo $insCategoria->listarCategoriaControlador("", "", "", "");
	
// Cargar el archivo footer.php usando la constante APP_PATH
$footer_path = APP_PATH . 'app/views/inc/footer.php';

if (file_exists($footer_path)) {
    require_once $footer_path;
} else {
    die("Error: No se pudo cargar el footer");
}
?>
<!-- Modal para ver imagen -->
<div class="modal fade" id="modalVerImagen" tabindex="-1" aria-labelledby="modalVerImagenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerImagenLabel">Imagen de Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagenModal" src="" alt="" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<!-- Scripts personalizados -->
<script>
    // Función para ver imagen en modal
    function verImagen(url, nombre) {
        document.getElementById('imagenModal').src = url;
        document.getElementById('imagenModal').alt = nombre;
        document.getElementById('modalVerImagenLabel').textContent = nombre;
        var modal = new bootstrap.Modal(document.getElementById('modalVerImagen'));
        modal.show();
    }
    
    
</script>

