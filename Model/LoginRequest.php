<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Model;

use Magento\Framework\Model\AbstractModel;

class LoginRequest extends AbstractModel
{
    public function _construct(): void
    {
        $this->_init(ResourceModel\LoginRequest::class);
    }

    public function setEmail(string $email): LoginRequest
    {
        return $this->setData('email', $email);
    }

    public function setType(string $type): LoginRequest
    {
        return $this->setData('type', $type);
    }

    public function setToken(string $token): LoginRequest
    {
        return $this->setData('token', $token);
    }

    public function setIsUsed(int $isUsed): LoginRequest
    {
        return $this->setData('is_used', $isUsed);
    }

    public function setExpiresAt(\DateTime $expiresAt): LoginRequest
    {
        return $this->setData('expires_at', $expiresAt);
    }

    public function getEmail(): ?string
    {
        return $this->getData('email');
    }

    public function getType(): ?string
    {
        return $this->getData('type');
    }

    public function getToken(): ?string
    {
        return $this->getData('token');
    }

    public function getIsUsed(): ?string
    {
        return $this->getData('is_used');
    }

    public function getExpiresAt(): ?string
    {
        return $this->getData('expires_at');
    }

    public function hasExpired(): bool
    {
        $now = new \DateTime;
        return $now->getTimestamp() > $this->getExpiresAt();
    }

    public function hasBeenUsed(): bool
    {
        return $this->getIsUsed() !== "0";
    }
}
