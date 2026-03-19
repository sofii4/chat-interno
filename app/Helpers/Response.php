<?php
declare(strict_types=1);
namespace App\Helpers;

class Response
{
    public static function json(\Psr\Http\Message\ResponseInterface $response, mixed $data, int $status = 200): \Psr\Http\Message\ResponseInterface
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function erro(\Psr\Http\Message\ResponseInterface $response, string $mensagem, int $status = 400): \Psr\Http\Message\ResponseInterface
    {
        return self::json($response, ['erro' => $mensagem], $status);
    }
}
