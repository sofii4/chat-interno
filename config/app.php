<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Projeto Chat',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => (bool) (getenv('APP_DEBUG') ?: true),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
];
