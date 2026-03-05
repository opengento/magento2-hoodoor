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
    #[\Override]
    public function _construct(): void
    {
        $this->_init(ResourceModel\LoginRequest::class);
    }

    /** Set the email address associated with this login request. */
    public function setEmail(string $email): LoginRequest
    {
        return $this->setData('email', $email);
    }

    /** Set the login request type (e.g. "customer" or "admin"). */
    public function setType(string $type): LoginRequest
    {
        return $this->setData('type', $type);
    }

    /** Set the unique token for this login request. */
    public function setToken(string $token): LoginRequest
    {
        return $this->setData('token', $token);
    }

    /** Set whether this login request has been consumed. */
    public function setIsUsed(int $isUsed): LoginRequest
    {
        return $this->setData('is_used', $isUsed);
    }

    /** Set the expiration date/time for this login request. */
    public function setExpiresAt(\DateTime $expiresAt): LoginRequest
    {
        return $this->setData('expires_at', $expiresAt);
    }

    /** Get the email address associated with this login request. */
    public function getEmail(): ?string
    {
        return $this->getData('email');
    }

    /** Get the login request type. */
    public function getType(): ?string
    {
        return $this->getData('type');
    }

    /** Get the unique token for this login request. */
    public function getToken(): ?string
    {
        return $this->getData('token');
    }

    /** Get the used status flag for this login request. */
    public function getIsUsed(): ?string
    {
        return $this->getData('is_used');
    }

    /** Get the expiration timestamp of this login request. */
    public function getExpiresAt(): ?string
    {
        return $this->getData('expires_at');
    }

    /** Check whether this login request has passed its expiration time. */
    public function hasExpired(): bool
    {
        $now = new \DateTime;
        return $now->getTimestamp() > $this->getExpiresAt();
    }

    /** Check whether this login request has already been consumed. */
    public function hasBeenUsed(): bool
    {
        return $this->getIsUsed() !== "0";
    }
}
