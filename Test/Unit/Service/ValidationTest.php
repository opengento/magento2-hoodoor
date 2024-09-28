<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Hoodoor\Model\Admin\User;
use Opengento\Hoodoor\Service\Validation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ValidationTest extends TestCase
{
    private CustomerRepositoryInterface|MockObject $customerRepositoryMock;

    private LoggerInterface|MockObject $loggerMock;

    private User|MockObject $userMock;

    private Validation $validationService;

    protected function setUp(): void
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->userMock = $this->createMock(User::class);

        $this->validationService = new Validation(
            $this->customerRepositoryMock,
            $this->loggerMock,
            $this->userMock
        );
    }

    public function testValidateReturnsTrueForActiveAdmin()
    {
        $email = 'admin@magento.test';
        $type = 'admin';

        $this->userMock->expects($this->once())
            ->method('loadByEmail')
            ->with($email)
            ->willReturnSelf();

        $this->userMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn(true);

        $this->assertTrue($this->validationService->validate($email, $type));
    }

    public function testValidateReturnsFalseForInactiveAdmin()
    {
        $email = 'inactive_admin@magento.test';
        $type = 'admin';

        $this->userMock->expects($this->once())
            ->method('loadByEmail')
            ->with($email)
            ->willReturnSelf();

        $this->userMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn(false);

        $this->assertFalse($this->validationService->validate($email, $type));
    }

    public function testValidateReturnsTrueForExistingCustomer()
    {
        $email = 'customer@magento.test';
        $type = 'customer';

        $customerMock = $this->createMock(CustomerInterface::class);
        $customerMock->method('getId')->willReturn(123);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email)
            ->willReturn($customerMock);

        $this->assertTrue($this->validationService->validate($email, $type));
    }

    public function testValidateReturnsFalseForNonExistingCustomer()
    {
        $email = 'nonexisting@magento.test';
        $type = 'customer';

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email)
            ->willThrowException(new NoSuchEntityException(__('No such entity')));

        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with('No such entity');

        $this->assertFalse($this->validationService->validate($email, $type));
    }
}
