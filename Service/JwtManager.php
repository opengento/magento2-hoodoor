<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\Hoodoor\Enum\Config;
use Psr\Log\LoggerInterface;

class JwtManager
{
    public function __construct(
        private readonly JWT $jwt,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly LoggerInterface $logger,
        private string $secretKey = ""
    ) {
        $this->secretKey = $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value);
    }

    public function generateToken(array $payload, int $expirationInSeconds): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $expirationInSeconds;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        return $this->jwt->encode($tokenPayload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): bool|\stdClass
    {
        try {
            return $this->jwt->decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return false;
    }
}

