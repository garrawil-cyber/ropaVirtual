<?php
/**
 * Script para crear imagen por defecto
 * Ejecutar una sola vez: php crear_imagen_default.php
 */

// Crear carpetas si no existen
$directorios = [
    'images',
    'img/productos',
    'img/categorias'
];

foreach ($directorios as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Carpeta creada: $dir\n";
    }
}

// Crear imagen por defecto
$width = 400;
$height = 500;
$image = imagecreatetruecolor($width, $height);

// Colores
$bg_color = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 150, 150, 150);

// Rellenar fondo
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Agregar texto
$text = "Sin imagen";
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;

imagestring($image, $font_size, $x, $y, $text, $text_color);

// Guardar imagen
imagepng($image, 'img/default.png');
imagedestroy($image);

echo "✓ Imagen por defecto creada: images/default.png\n";
echo "\n¡Listo! Ahora puedes usar el sistema.\n";
?>