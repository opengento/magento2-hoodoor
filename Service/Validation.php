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
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly User $user
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
