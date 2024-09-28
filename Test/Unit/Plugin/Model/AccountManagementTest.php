<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Plugin\Model;

use Magento\Customer\Model\AccountManagement as SubjectAccountManagement;
use Opengento\Hoodoor\Plugin\Model\AccountManagement;
use PHPUnit\Framework\TestCase;

class AccountManagementTest extends TestCase
{
    private AccountManagement $plugin;

    protected function setUp(): void
    {
        $this->plugin = new AccountManagement();
    }

    public function testBeforeChangePasswordThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access denied.');

        $this->plugin->beforeChangePassword(
            $this->createMock(SubjectAccountManagement::class),
            'customer@example.com',
            'current_password',
            'new_password'
        );
    }

    public function testBeforeChangePasswordByIdThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access denied.');

        $this->plugin->beforeChangePasswordById(
            $this->createMock(SubjectAccountManagement::class),
            123,
            'current_password',
            'new_password'
        );
    }
}
