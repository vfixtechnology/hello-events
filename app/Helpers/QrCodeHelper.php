<?php

namespace App\Helpers;

class QrCodeHelper
{
    public static function generateSvg(string $content, int $size = 200): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd
        );
        $writer = new \BaconQrCode\Writer($renderer);

        return $writer->writeString($content);
    }

    public static function generateBase64(string $content, int $size = 200): string
    {
        return base64_encode(self::generateSvg($content, $size));
    }

    public static function generateDataUri(string $content, int $size = 200): string
    {
        return 'data:image/svg+xml;base64,'.self::generateBase64($content, $size);
    }
}
