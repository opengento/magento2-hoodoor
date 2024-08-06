<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Hoodoor\Model\Admin\User;

class Validation
{
    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Opengento\Hoodoor\Model\Admin\User $user
     */
    public function __construct( //phpcs:ignore
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly User $user
    ) {
    }

    /**
     * Customer Validation
     *
     * @param string $email
     * @param string $type
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
