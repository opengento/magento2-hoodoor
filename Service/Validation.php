<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\PasswordLessLogin\Model\Admin\User;

class Validation
{
    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Opengento\PasswordLessLogin\Model\Admin\User $user
     */
    public function __construct(
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly User $user
    ) {
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(string $email, string $type): bool
    {
        if ($type === 'admin') {
            $user = $this->user->loadByEmail($email);
            return $user && $user->getIsActive();
        }
        return $this->customerRepository->get($email)->getId() !== null;
    }
}
