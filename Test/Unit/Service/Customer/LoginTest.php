<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Opengento\Hoodoor\Service\Customer\Login;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoginTest extends TestCase
{
    private CustomerRepositoryInterface|MockObject $customerRepositoryMock;

    private AccountManagementInterface|MockObject $customerAccountManagementMock;

    private CredentialsValidator|MockObject $credentialsValidatorMock;

    private CustomerRegistry|MockObject $customerRegistryMock;

    private Encryptor|MockObject $encryptorMock;

    private SessionCleanerInterface|MockObject $sessionCleanerMock;

    private Session|MockObject $sessionMock;

    private CookieMetadataFactory|MockObject $cookieMetadataFactoryMock;

    private PhpCookieManager|MockObject $cookieMetadataManagerMock;

    private LoggerInterface|MockObject $loggerMock;

    private Login $loginService;

    protected function setUp(): void
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerAccountManagementMock = $this->createMock(AccountManagementInterface::class);
        $this->credentialsValidatorMock = $this->createMock(CredentialsValidator::class);
        $this->customerRegistryMock = $this->createMock(CustomerRegistry::class);
        $this->encryptorMock = $this->createMock(Encryptor::class);
        $this->sessionCleanerMock = $this->createMock(SessionCleanerInterface::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->cookieMetadataFactoryMock = $this->createMock(CookieMetadataFactory::class);
        $this->cookieMetadataManagerMock = $this->createMock(PhpCookieManager::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->loginService = new Login(
            $this->customerRepositoryMock,
            $this->customerAccountManagementMock,
            $this->credentialsValidatorMock,
            $this->customerRegistryMock,
            $this->encryptorMock,
            $this->sessionCleanerMock,
            $this->sessionMock,
            $this->cookieMetadataFactoryMock,
            $this->cookieMetadataManagerMock,
            $this->loggerMock
        );
    }

    public function testPerformReturnsTrueOnSuccessfulLogin()
    {
        $data = ['email' => 'customer@magento.test', 'token' => 'secure_token'];

        $customerMock = $this->createMock(CustomerInterface::class);
        $customerMock->method('getId')->willReturn(123);

        $customerSecureMock = $this->getMockBuilder(CustomerSecure::class)
            ->disableOriginalConstructor()
            ->addMethods(['setPasswordHash'])
            ->getMock();

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($data['email'])
            ->willReturn($customerMock);

        $this->credentialsValidatorMock->expects($this->once())
            ->method('checkPasswordDifferentFromEmail')
            ->with($data['email'], $data['token']);

        $this->customerRegistryMock->expects($this->once())
            ->method('retrieveSecureData')
            ->with(123)
            ->willReturn($customerSecureMock);

        $this->encryptorMock->expects($this->once())
            ->method('getHash')
            ->willReturn('hashed_password');

        $customerSecureMock->expects($this->once())
            ->method('setPasswordHash')
            ->with('hashed_password');

        $this->sessionCleanerMock->expects($this->once())
            ->method('clearFor')
            ->with(123);

        $this->customerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($customerMock);

        $this->customerAccountManagementMock->expects($this->once())
            ->method('authenticate')
            ->with($data['email'], $data['token'])
            ->willReturn($customerMock);

        $this->sessionMock->expects($this->once())
            ->method('setCustomerDataAsLoggedIn')
            ->with($customerMock);

        $this->assertTrue($this->loginService->perform($data));
    }

    public function testPerformReturnsFalseOnException()
    {
        $data = ['email' => 'invalid@magento.test', 'token' => 'secure_token'];

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($data['email'])
            ->willThrowException(new NoSuchEntityException(__('Invalid request')));

        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with('Invalid request');

        $this->assertFalse($this->loginService->perform($data));
    }
}
