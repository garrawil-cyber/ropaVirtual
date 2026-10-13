<?php
// Verificar si el usuario está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['usuario']) || empty($_SESSION['id']) || empty($_SESSION['usuario'])) {
    echo "<script>
        window.location.href='".APP_URL."login/';
    </script>";
    exit();
}
?>

<?php require_once APP_PATH . "app/views/inc/head.php"; ?>

<!-- CONTENEDOR PRINCIPAL CENTRADO -->
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="dashboard-container text-center">

                <?php
                // Validación y visualización segura de foto de perfil
                $foto = !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'default.png';
                $foto_url = APP_URL . 'app/views/fotos/' . $foto;
                
                // Verificar si el archivo existe
                $foto_path = $_SERVER['DOCUMENT_ROOT'] . '/ropaVirtual_mvc/app/views/fotos/' . $foto;
                
                if (file_exists($foto_path) && is_file($foto_path)) {
                    echo '<img class="profile-image" src="' . $foto_url . '" alt="Foto de perfil">';
                } else {
                    echo '<img class="profile-image" src="' . APP_URL . 'app/views/fotos/default.png" alt="Foto de perfil por defecto">';
                }
                ?>

                <h1 class="welcome-title">Bienvenido al Panel</h1>
                <div class="user-name">
                    <?php echo htmlspecialchars($_SESSION["nombre"] . " " . $_SESSION["apellido"]); ?>
                </div>
                <p class="welcome-text">
                    Acceda a todas las herramientas necesarias para administrar su sitio web de manera eficiente.
                </p>

                <a href="<?php echo APP_URL; ?>userList/" class="btn-start">
                    Acceder al Panel <i class="fas fa-arrow-right"></i>
                </a>

            </div>
        </div>
    </div>
</div>

 <link rel="stylesheet" href="<?= APP_URL ?>app/views/css/dashboard-view.css">

