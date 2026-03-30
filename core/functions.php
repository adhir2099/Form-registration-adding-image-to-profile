<?php

    function makeImage(string $character): string
    {
        $character = strtoupper(trim($character));
        if ($character === '') {
            throw new InvalidArgumentException('Character cannot be empty');
        }
        $character = $character[0];

        $baseDir = __DIR__ . '/../assets/';
        $imageDir = $baseDir . 'img/profile/';
        $fontPath = $baseDir . 'fonts/Pokemon.ttf';

        if (!file_exists($fontPath)) {
            throw new RuntimeException('Font file not found');
        }

        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $size = 200;
        $image = imagecreatetruecolor($size, $size);

        $red   = random_int(50, 200);
        $green = random_int(50, 200);
        $blue  = random_int(50, 200);

        $bgColor   = imagecolorallocate($image, $red, $green, $blue);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $bgColor);

        $fontSize = 100;
        $angle = 0;
        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $character);

        $textWidth  = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        $x = ($size - $textWidth) / 2;
        $y = ($size + $textHeight) / 2;

        imagettftext($image, $fontSize, $angle, (int)$x, (int)$y, $textColor, $fontPath, $character);

        $fileName = uniqid('avatar_', true) . '.png';
        $filePath = $imageDir . $fileName;

        if (!imagepng($image, $filePath)) {
            throw new RuntimeException('Failed to save image');
        }

        return 'assets/img/profile/' . $fileName;
    }