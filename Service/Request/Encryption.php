<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\Hoodoor\Enum\Config;

class Encryption
{
    /**
     * @var string
     */
    protected const CIPHER_ALGO_TYPE = 'aes-256-cbc';

    /**
     * @var string
     */
    protected string $secretKey;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig
    ) {
        $this->secretKey = $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value) ?: "";
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    public function encrypt($data, $key): string
    {
        $iv = \openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_ALGO_TYPE));
        $encrypted = \openssl_encrypt($data, self::CIPHER_ALGO_TYPE, $key, 0, $iv);
        return \base64_encode($encrypted . '::' . $iv);
    }

    /**
     * @param $data
     * @param $key
     * @return false|string
     */
    public function decrypt($data, $key): bool|string
    {
        list($encrypted_data, $iv) = \explode('::', \base64_decode($data), 2);
        return \openssl_decrypt($encrypted_data, self::CIPHER_ALGO_TYPE, $key, 0, $iv);
    }
}
