<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Hoodoor\Model\Admin\User;
use Psr\Log\LoggerInterface;

class Validation
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly LoggerInterface $logger,
        private readonly User $user
    ) {
    }

    public function validate(string $email, string $type): bool
    {
        try {
            if ($type === 'admin') {
                $user = $this->user->loadByEmail($email);
                return $user && $user->getIsActive();
            }
            return $this->customerRepository->get($email)->getId() !== null;
        } catch (NoSuchEntityException $e) {
            $this->logger->debug($e->getMessage());
        }
        return false;
    }
}
