<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\Model;

class AccountManagement
{
    /**
     * Intercept Change Password
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeChangePassword(
        \Magento\Customer\Model\AccountManagement $subject,
        string $email,
        string $currentPassword,
        string $newPassword
    ): void {
        throw new \Exception(_('Access denied.'));
    }

    /**
     * Intercept Change Password By Id
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeChangePasswordById(
        \Magento\Customer\Model\AccountManagement $subject,
        int|string $customerId,
        string $currentPassword,
        string $newPassword
    ): void {
        throw new \Exception(_('Access denied.'));
    }
}
