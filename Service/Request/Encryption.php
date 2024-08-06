<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url\DecoderInterface;
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
     * @param \Magento\Framework\Url\DecoderInterface $decoder
     */
    public function __construct( //phpcs:ignore
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly DecoderInterface $decoder
    ) {
        $this->secretKey = $this->scopeConfig
            ->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value) ?: "";
    }

    /**
     * Encrypt Data
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    public function encrypt(string $data, string $key): string
    {
        $iv = \openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_ALGO_TYPE));
        $encrypted = \openssl_encrypt($data, self::CIPHER_ALGO_TYPE, $key, 0, $iv);
        return \base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Decrypt Data
     *
     * @param string $data
     * @param string $key
     * @return false|string
     */
    public function decrypt(string $data, string $key): bool|string
    {
        list($encrypted_data, $iv) = \explode('::', $this->decoder->decode($data), 2);
        return \openssl_decrypt($encrypted_data, self::CIPHER_ALGO_TYPE, $key, 0, $iv);
    }
}
