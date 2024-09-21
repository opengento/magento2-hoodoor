<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Exception\RequestException;

class Encryption
{
    protected string $secretKey;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
    ) {
        $this->secretKey = $this->scopeConfig
            ->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value) ?: "";
    }

    public function encrypt(string $data): string
    {
        return $this->encryptor->encrypt($data . '::' . $this->secretKey);
    }

    public function decrypt(string $data): bool|string
    {
        $decryptedData = explode('::', $this->encryptor->decrypt($data), 2);
        if(!$this->isSecretKeyValid(end($decryptedData))) {
            throw new RequestException(_('Something went wrong while processing. Try again.'));
        }
        return reset($decryptedData);
    }

    private function isSecretKeyValid(string $secretKey): bool
    {
        return $this->secretKey === $secretKey;
    }
}
