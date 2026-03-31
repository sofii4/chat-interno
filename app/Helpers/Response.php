<?php
declare(strict_types=1);
namespace App\Helpers;

class Response
{
    public static function json(\Psr\Http\Message\ResponseInterface $response, mixed $data, int $status = 200): \Psr\Http\Message\ResponseInterface
    {
        try {
            $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            error_log('Falha ao serializar JSON: ' . $e->getMessage());
            $payload = '{"erro":"Falha ao serializar resposta JSON"}';
            $status = 500;
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function erro(\Psr\Http\Message\ResponseInterface $response, string $mensagem, int $status = 400): \Psr\Http\Message\ResponseInterface
    {
        return self::json($response, ['erro' => $mensagem], $status);
    }
}
