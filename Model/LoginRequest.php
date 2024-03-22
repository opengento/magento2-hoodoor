<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Model;

use Magento\Framework\Model\AbstractModel;

class LoginRequest extends AbstractModel
{
    /**
     * @return void
     */
    public function _construct(): void
    {
        $this->_init(\Opengento\PasswordLessLogin\Model\ResourceModel\LoginRequest::class);
    }

    /**
     * @param string $email
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest
     */
    public function setEmail(string $email): LoginRequest
    {
        return $this->setData('email', $email);
    }

    /**
     * @param string $token
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest
     */
    public function setToken(string $token): LoginRequest
    {
        return $this->setData('token', $token);
    }

    /**
     * @param int $isUsed
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest
     */
    public function setIsUsed(int $isUsed): LoginRequest
    {
        return $this->setData('is_used', $isUsed);
    }

    /**
     * @param \DateTime $expiresAt
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest
     */
    public function setExpiresAt(\DateTime $expiresAt): LoginRequest
    {
        return $this->setData('expires_at', $expiresAt);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData('email');
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->getData('token');
    }

    /**
     * @return string|null
     */
    public function getIsUsed(): ?string
    {
        return $this->getData('is_used');
    }

    /**
     * @return string|null
     */
    public function getExpiresAt(): ?string
    {
        return $this->getData('expires_at');
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        $now = new \DateTime;
        return $now->getTimestamp() > $this->getExpiresAt();
    }

    /**
     * @return bool
     */
    public function hasBeenUsed(): bool
    {
        return $this->getIsUsed() !== "0";
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isValidToken(string $token): bool
    {
        return $this->getToken() === $token;
    }
}
