<?php
// Verificar que los archivos existan antes de incluirlos
$jsFiles = [
    'ajax.js',
    'main.js'
];

foreach ($jsFiles as $jsFile) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/ropaVirtual_mvc/app/views/js/' . $jsFile;
    if (file_exists($filePath)) {
        echo '<script src="' . APP_URL . 'app/views/js/' . $jsFile . '"></script>' . "\n";
    } else {
        // Log error pero no rompe la página
        error_log("Archivo JS no encontrado: " . $jsFile);
    }
}
?>