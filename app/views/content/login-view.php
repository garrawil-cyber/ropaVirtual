<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>app/views/css/login-view.css">
    
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo-container">
                    <div class="logo-wrapper">
                        <img src="<?php echo APP_URL; ?>app/views/img/seg.jpg" alt="Logo del Sistema">
                    </div>
                </div>
                <h2>Bienvenido</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <form id="loginForm" method="POST" action="" novalidate>
                    <div class="form-group">
                        <label for="login_usuario" class="form-label">
                            <i class="fas fa-user-circle"></i> Usuario
                        </label>
                        <div class="input-group-custom">
                            <input type="text" 
                                   class="form-control" 
                                   id="login_usuario" 
                                   name="login_usuario" 
                                   pattern="[a-zA-Z0-9]{5,20}" 
                                   minlength="5"
                                   maxlength="20" 
                                   required 
                                   autocomplete="username"
                                   placeholder="Ingrese su usuario">
                            <i class="input-icon fas fa-user"></i>
                        </div>
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i>
                            El usuario debe tener entre 5 y 20 caracteres alfanuméricos
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login_clave" class="form-label">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <div class="input-group-custom">
                            <input type="password" 
                                   class="form-control" 
                                   id="login_clave" 
                                   name="login_clave" 
                                   pattern="[a-zA-Z0-9$@.\-]{7,100}" 
                                   minlength="7"
                                   maxlength="100" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Ingrese su contraseña">
                            <i class="input-icon fas fa-key"></i>
                        </div>
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i>
                            La contraseña debe tener al menos 7 caracteres válidos
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </span>
                        <div class="loading-spinner"></div>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>
                    <i class="fas fa-shield-alt"></i>
                    Sistema seguro y protegido
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo APP_URL; ?>app/views/js/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const usuarioInput = document.getElementById('login_usuario');
            const claveInput = document.getElementById('login_clave');
            const btnLogin = document.querySelector('.btn-login');

            // Función para validar un campo individual
            function validarCampo(input) {
                const valor = input.value.trim();
                const esValido = input.checkValidity();
                
                if (valor.length === 0) {
                    input.classList.remove('is-invalid');
                    return false;
                }
                
                if (!esValido) {
                    input.classList.add('is-invalid');
                    return false;
                } else {
                    input.classList.remove('is-invalid');
                    return true;
                }
            }

            // Validación en tiempo real para el usuario
            usuarioInput.addEventListener('input', function() {
                validarCampo(this);
            });

            usuarioInput.addEventListener('blur', function() {
                if (this.value.trim().length > 0) {
                    validarCampo(this);
                }
            });

            // Validación en tiempo real para la clave
            claveInput.addEventListener('input', function() {
                validarCampo(this);
            });

            claveInput.addEventListener('blur', function() {
                if (this.value.trim().length > 0) {
                    validarCampo(this);
                }
            });

            // Validación del formulario al enviar
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const usuarioValido = validarCampo(usuarioInput);
                const claveValida = validarCampo(claveInput);

                // Marcar como validado
                form.classList.add('was-validated');

                // Si ambos campos son válidos, enviar el formulario
                if (usuarioValido && claveValida && form.checkValidity()) {
                    // Mostrar estado de carga
                    btnLogin.classList.add('loading');
                    btnLogin.disabled = true;
                    
                    // Enviar el formulario
                    setTimeout(() => {
                        form.submit();
                    }, 300);
                } else {
                    // Mostrar mensaje de error si los campos no son válidos
                    if (!usuarioValido || !claveValida) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            text: 'Por favor, corrija los errores en el formulario antes de continuar',
                            confirmButtonColor: '#667eea',
                            confirmButtonText: 'Entendido'
                        });
                    }
                }
            });

            // Limpiar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);

            // Efecto de enfoque automático
            if (usuarioInput) {
                usuarioInput.focus();
            }

            // Saltar al siguiente campo con Enter
            usuarioInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    claveInput.focus();
                }
            });

            // Submit al presionar Enter en el campo de contraseña
            claveInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>

<?php
    if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
        $insLogin->iniciarSesionControlador();
    }
?>

</body>
</html>