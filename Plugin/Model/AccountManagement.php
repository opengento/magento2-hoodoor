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
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeChangePassword(
        \Magento\Customer\Model\AccountManagement $subject,
        string $email,
        string $currentPassword,
        string $newPassword
    ): void {
        throw new \Exception(_('Access denied.')); //phpcs:ignore
    }

    /**
     * Intercept Change Password By Id
     *
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param int|string $customerId
     * @param string $currentPassword
     * @param string $newPassword
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeChangePasswordById(
        \Magento\Customer\Model\AccountManagement $subject,
        int|string $customerId,
        string $currentPassword,
        string $newPassword
    ): void {
        throw new \Exception(_('Access denied.')); //phpcs:ignore
    }
}
