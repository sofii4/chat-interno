<?php
declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ResponseInterface;

class TemplateRenderer
{
    public static function render(ResponseInterface $response, string $templatePath, array $data = []): ResponseInterface
    {
        extract($data, EXTR_SKIP);

        ob_start();
        include $templatePath;
        $html = (string) ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }
}
