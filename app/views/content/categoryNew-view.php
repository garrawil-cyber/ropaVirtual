<?php
// Incluir navbar.php usando ruta del sistema de archivos
$navbar_path = __DIR__ . '/../inc/navbar.php';
if (file_exists($navbar_path)) {
    require_once $navbar_path;
} else {
    // Si no existe, mostrar error controlado
    die("Error: No se pudo cargar el navbar");
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4 shadow">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="mb-0">
                        <i class="fas fa-folder-plus me-2"></i>Nueva Categoría
                    </h5>
                    <a href="<?php echo APP_URL; ?>categoryList/" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>

                <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/categoriaAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    
                    <input type="hidden" name="modulo_categoria" value="registrar">

                    <div class="row g-3">
                        
                        <!-- Nombre de la Categoría -->
                        <div class="col-md-6">
                            <label for="categoria_nombre" class="form-label">
                                <i class="fas fa-tag me-2"></i>Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="categoria_nombre" id="categoria_nombre" 
                                   placeholder="Ej: Vestidos, Pantalones, etc." maxlength="100" required>
                            <div class="form-text">Ingrese el nombre de la categoría (3-100 caracteres)</div>
                        </div>

                        <!-- Estado Activo -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="categoria_estado" 
                                       id="categoria_estado" checked>
                                <label class="form-check-label" for="categoria_estado">
                                    Categoría activa
                                </label>
                            </div>
                            <div class="form-text">Marca si la categoría estará visible en el sistema</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label for="categoria_descripcion" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Descripción
                            </label>
                            <textarea class="form-control" name="categoria_descripcion" id="categoria_descripcion" 
                                      rows="3" placeholder="Describe brevemente la categoría..." maxlength="500"></textarea>
                            <div class="form-text">Descripción opcional de la categoría (máximo 500 caracteres)</div>
                        </div>

                        <!-- Imagen de la Categoría -->
                        <div class="col-12">
                            <label for="categoria_foto" class="form-label">
                                <i class="fas fa-image me-2"></i>Imagen de la Categoría
                            </label>
                            <input type="file" class="form-control" name="categoria_foto" id="categoria_foto" 
                                   accept=".jpg, .png, .jpeg">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG | Peso máximo: 5MB
                            </div>
                        </div>

                        <!-- Vista previa de imagen -->
                        <div class="col-12">
                            <div id="preview-container" style="display: none;">
                                <label class="form-label">Vista previa:</label>
                                <div class="border rounded p-2 text-center bg-white">
                                    <img id="preview-image" src="" alt="Vista previa" 
                                         style="max-width: 200px; max-height: 200px; object-fit: contain;">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-eraser me-2"></i>Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Categoría
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- Script para vista previa de imagen -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputFoto = document.getElementById('categoria_foto');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    if(inputFoto) {
        inputFoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if(file) {
                // Validar tipo de archivo
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if(!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no válido',
                        text: 'Por favor selecciona una imagen JPG o PNG'
                    });
                    inputFoto.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                // Validar tamaño (5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB en bytes
                if(file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy grande',
                        text: 'La imagen no debe superar los 5MB'
                    });
                    inputFoto.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                // Mostrar vista previa
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }

    // Limpiar vista previa al resetear formulario
    const form = document.querySelector('.FormularioAjax');
    if(form) {
        form.addEventListener('reset', function() {
            previewContainer.style.display = 'none';
            previewImage.src = '';
        });
    }
});
</script>

<!-- Script para manejar formularios AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let formularios_ajax = document.querySelectorAll(".FormularioAjax");

    formularios_ajax.forEach(function(form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            // Verificar si es un formulario de eliminación (tiene el valor "eliminar")
            let modulo = form.querySelector('input[name="modulo_categoria"]');
            let esEliminacion = modulo && modulo.value === "eliminar";

            // Solo pedir confirmación para eliminaciones
            if (esEliminacion) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede revertir",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarFormulario(form);
                    }
                });
            } else {
                // Para registros y actualizaciones, enviar directamente
                enviarFormulario(form);
            }
        });
    });

    function enviarFormulario(form) {
        let data = new FormData(form);
        let method = form.getAttribute("method");
        let action = form.getAttribute("action");

        let headers = new Headers();
        
        let config = {
            method: method,
            headers: headers,
            mode: 'cors',
            cache: 'no-cache',
            body: data
        };

        fetch(action, config)
            .then(response => response.json())
            .then(data => {
                return alertas_ajax(data);
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Error:', error);
            });
    }

    function alertas_ajax(alerta) {
        if (alerta.tipo == "simple") {
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            });
        } else if (alerta.tipo == "recargar") {
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else if (alerta.tipo == "limpiar") {
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.FormularioAjax').reset();
                    // Limpiar vista previa de imagen
                    let previewContainer = document.getElementById('preview-container');
                    if(previewContainer) {
                        previewContainer.style.display = 'none';
                    }
                }
            });
        } else if (alerta.tipo == "redireccionar") {
            window.location.href = alerta.url;
        }
    }
});
</script>