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
</section>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 rounded p-4 shadow">
            <div class="card shadow mb-4">

             <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Usuarios</h5>
                
            </div>

                <div class="card-body">
                    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="modulo_usuario" value="registrar">

                        <div class="row">
							<div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Información Personal</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_nombre" class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="usuario_nombre" name="usuario_nombre" 
                                       pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required 
                                       placeholder="Ingrese los nombres">
                                <div class="invalid-feedback">
                                    Los nombres deben tener entre 3 y 40 caracteres (solo letras y espacios)
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_apellido" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="usuario_apellido" name="usuario_apellido" 
                                       pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required 
                                       placeholder="Ingrese los apellidos">
                                <div class="invalid-feedback">
                                    Los apellidos deben tener entre 3 y 40 caracteres (solo letras y espacios)
                                </div>
                            </div>
                        </div>

                        <div class="row">
							<div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-key me-2"></i>Información de Cuenta</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_usuario" class="form-label">Usuario <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="usuario_usuario" name="usuario_usuario" 
                                       pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required 
                                       placeholder="Ingrese el nombre de usuario">
                                <div class="invalid-feedback">
                                    El usuario debe tener entre 4 y 20 caracteres alfanuméricos
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_email" class="form-label">Email</label>
                                <input class="form-control" type="email" id="usuario_email" name="usuario_email" 
                                       maxlength="70" placeholder="usuario@ejemplo.com">
                                <div class="invalid-feedback">
                                    Por favor ingrese un email válido
                                </div>
                            </div>
                        </div>

                        <div class="row">
							<div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-lock me-2"></i>Seguridad</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_clave_1" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input class="form-control" type="password" id="usuario_clave_1" name="usuario_clave_1" 
                                       pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required 
                                       placeholder="Mínimo 7 caracteres">
                                <div class="invalid-feedback">
                                    La contraseña debe tener al menos 7 caracteres (letras, números y los caracteres especiales $ @ . -)
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="usuario_clave_2" class="form-label">Repetir Contraseña <span class="text-danger">*</span></label>
                                <input class="form-control" type="password" id="usuario_clave_2" name="usuario_clave_2" 
                                       pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required 
                                       placeholder="Repita la contraseña">
                                <div class="invalid-feedback">
                                    Las contraseñas deben coincidir
                                </div>
                            </div>
                        </div>

 <!-- Foto de Perfil -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fas fa-camera"></i>
                                    Foto de Perfil
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="usuario_foto" class="form-label">
                                        Seleccionar Imagen
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="usuario_foto" name="usuario_foto" 
                                               accept=".jpg, .png, .jpeg" onchange="previewImage(this)">
                                        <label class="custom-file-label" for="usuario_foto" id="fileLabel">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            Seleccione una imagen
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 5MB
                                    </small>
                                </div>
                                <!-- Vista previa de la imagen -->
                                <div class="text-center mt-3" id="imagePreviewContainer" style="display: none;">
                                    <p class="text-muted mb-3 fw-semibold">
                                        <i class="fas fa-eye me-2"></i>Vista previa:
                                    </p>
                                    <img id="imagePreview" class="img-thumbnail rounded-circle" alt="Vista previa">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeImage()">
                                        <i class="fas fa-times"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-5">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success me-3">
                                    <i class="fas fa-save"></i>
                                    Guardar Usuario
                                </button>
                                <a href="<?php echo APP_URL; ?>userList/" class="btn btn-primary">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>


 <link rel="stylesheet" href="<?= APP_URL ?>app/views/css/userNew-view.css">


<script>
// Función para vista previa de imagen
function previewImage(input) {
    const file = input.files[0];
    const previewContainer = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');
    const fileLabel = document.getElementById('fileLabel');
    
    if (file) {
        // Validar tamaño del archivo (5MB máximo)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo muy grande',
                text: 'La imagen no debe superar los 5MB',
                confirmButtonColor: '#3085d6'
            });
            input.value = '';
            return;
        }
        
        // Validar tipo de archivo
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Formato no válido',
                text: 'Solo se permiten archivos JPG, JPEG y PNG',
                confirmButtonColor: '#3085d6'
            });
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
        
        fileLabel.innerHTML = `<i class="fas fa-file-image me-2"></i>${file.name}`;
    }
}

// Función para eliminar imagen seleccionada
function removeImage() {
    const input = document.getElementById('usuario_foto');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const fileLabel = document.getElementById('fileLabel');
    
    input.value = '';
    previewContainer.style.display = 'none';
    fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>Seleccione una imagen';
}
</script>

