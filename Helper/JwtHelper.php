<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public function encode(array $payload, string $key, string $alg): string
    {
        return JWT::encode($payload, $key, $alg);
    }

    public function decode(string $jwt, Key $key): \stdClass
    {
        return JWT::decode($jwt, $key);
    }
}
