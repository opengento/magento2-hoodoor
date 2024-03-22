<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Service\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;

class Validation
{
    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(string $email): bool
    {
        return $this->customerRepository->get($email)->getId() !== null;
    }
}
