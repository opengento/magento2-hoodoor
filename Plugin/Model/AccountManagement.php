<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Plugin\Model;

class AccountManagement
{
    /**
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param $email
     * @param $currentPassword
     * @param $newPassword
     * @return void
     * @throws \Exception
     */
    public function beforeChangePassword(
        \Magento\Customer\Model\AccountManagement $subject,
        $email,
        $currentPassword,
        $newPassword
    ): void
    {
        throw new \Exception(_('Access denied.'));
    }

    /**
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param $customerId
     * @param $currentPassword
     * @param $newPassword
     * @return void
     * @throws \Exception
     */
    public function beforeChangePasswordById(
        \Magento\Customer\Model\AccountManagement $subject,
        $customerId,
        $currentPassword,
        $newPassword
    ): void
    {
        throw new \Exception(_('Access denied.'));
    }
}
