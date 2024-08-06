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
    /**
     * Construct
     *
     * @return void
     */
    public function _construct(): void
    {
        $this->_init(ResourceModel\LoginRequest::class);
    }

    /**
     * Set Email
     *
     * @param string $email
     * @return \Opengento\Hoodoor\Model\LoginRequest
     */
    public function setEmail(string $email): LoginRequest
    {
        return $this->setData('email', $email);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return \Opengento\Hoodoor\Model\LoginRequest
     */
    public function setType(string $type): LoginRequest
    {
        return $this->setData('type', $type);
    }

    /**
     * Set Token
     *
     * @param string $token
     * @return \Opengento\Hoodoor\Model\LoginRequest
     */
    public function setToken(string $token): LoginRequest
    {
        return $this->setData('token', $token);
    }

    /**
     * Set Is Used
     *
     * @param int $isUsed
     * @return \Opengento\Hoodoor\Model\LoginRequest
     */
    public function setIsUsed(int $isUsed): LoginRequest
    {
        return $this->setData('is_used', $isUsed);
    }

    /**
     * Set Expires At
     *
     * @param \DateTime $expiresAt
     * @return \Opengento\Hoodoor\Model\LoginRequest
     */
    public function setExpiresAt(\DateTime $expiresAt): LoginRequest
    {
        return $this->setData('expires_at', $expiresAt);
    }

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData('email');
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData('type');
    }

    /**
     * Get Token
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->getData('token');
    }

    /**
     * Get Is Used
     *
     * @return string|null
     */
    public function getIsUsed(): ?string
    {
        return $this->getData('is_used');
    }

    /**
     * Get Expires At
     *
     * @return string|null
     */
    public function getExpiresAt(): ?string
    {
        return $this->getData('expires_at');
    }

    /**
     * Has Expired
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        $now = new \DateTime;
        return $now->getTimestamp() > $this->getExpiresAt();
    }

    /**
     * Has Been Used
     *
     * @return bool
     */
    public function hasBeenUsed(): bool
    {
        return $this->getIsUsed() !== "0";
    }
}
