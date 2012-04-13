<?php
    error_reporting(E_STRICT);

    // Set the content-type
    header("Content-type: image/png");
    
    // Create the image
    $im = @imagecreatefromgif('GD/bg.gif');
    
    // Create some colors
    $black = imagecolorallocate($im, 0, 0, 0);
    $blue  = imagecolorallocate($im, 0, 0, 255);
    $white = imagecolorallocate($im,255,255,255);
    
    // Replace by your own font full path and name
    $path  = substr($_SERVER['SCRIPT_FILENAME'], 0, 
                    strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
    $font  = $path.'/GD/ae_AlHor.ttf';
    
    // UTF-8 charset
    $text = 'بسم الله الرحمن الرحيم';
    imagefill($im,0,0,$white);
    imagettftext($im, 20, 0, 10, 50, $blue, $font, 'UTF-8:');
    imagettftext($im, 20, 0, 200, 50, $black, $font, $text);

    include('../Arabic.php');
    $Arabic = new Arabic('ArGlyphs');

    $text = 'بسم الله الرحمن الرحيم';
    $text = $Arabic->utf8Glyphs($text);

    imagettftext($im, 20, 0, 10, 100, $blue, $font, 'ArGlyphs:');
    imagettftext($im, 20, 0, 200, 100, $black, $font, $text);
    
    // Using imagepng() results in clearer text compared with imagejpeg()
    imagepng($im);
    imagedestroy($im);
?>
