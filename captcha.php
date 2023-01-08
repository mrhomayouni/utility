<?php

header('Content-type: image/jpeg');
$time = time();
$image = imagecreate(200, 100);
$color_line = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
$color_pixle = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
$color_background = imagecolorallocate($image, 0, 0, 0);
$text_color = imagecolorallocate($image, 255, 255, 255);
imagefilledrectangle($image, 0, 0, 200, 100, $color_background);

for ($i = 1; $i <= 10; $i++) {
    imageline($image, rand(0, 100), rand(0, 100), 200, rand(0, 100), $color_line);
}
for ($i = 1; $i <= 1000; $i++) {
    imagesetpixel($image, rand(0, 200), rand(0, 100), $color_pixle);
}

$letters = "0123456789";
$len = strlen($letters);
$word = "";
$font = "public/app/fonts/Fairytail-p7P41.ttf";
for ($i = 1; $i < 4; $i++) {
    $letter = $letters[rand(0, $len - 1)];
    imagettftext($image, 60, rand(20, 40), 5 + ($i * 45), 90, $text_color, $font, $letter);
    $word = $word . $letter;
    $_SESSION["captcha"] = $word;
}
$color_line = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
for ($i = 1; $i <= 10; $i++) {
    imageline($image, rand(0, 100), rand(0, 100), 200, rand(0, 100), $color_line);
}
imagepng($image);



