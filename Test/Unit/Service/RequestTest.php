<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service;

use DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Exception\RequestException;
use Opengento\Hoodoor\Model\LoginRequest;
use Opengento\Hoodoor\Model\LoginRequestFactory;
use Opengento\Hoodoor\Model\LoginRequestRepository;
use Opengento\Hoodoor\Service\Request;
use Opengento\Hoodoor\Service\Validation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RequestTest extends TestCase
{
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    private LoginRequestFactory|MockObject $loginRequestFactoryMock;

    private LoginRequestRepository|MockObject $loginRequestRepositoryMock;

    private Validation|MockObject $validationServiceMock;

    private Random|MockObject $randomMock;

    private LoggerInterface $loggerMock;

    private Request $requestService;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->loginRequestFactoryMock = $this->createMock(LoginRequestFactory::class);
        $this->loginRequestRepositoryMock = $this->createMock(LoginRequestRepository::class);
        $this->validationServiceMock = $this->createMock(Validation::class);
        $this->randomMock = $this->createMock(Random::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->requestService = new Request(
            $this->scopeConfigMock,
            $this->loginRequestFactoryMock,
            $this->loginRequestRepositoryMock,
            $this->validationServiceMock,
            $this->randomMock,
            $this->loggerMock
        );
    }

    public function testCreateReturnsTrueWhenRequestIsValid()
    {
        $email = 'customer@magento.test';
        $type = 'login';
        $uniqueHash = 'uniqueHash';
        $maxTimeExpiration = '+1 hour';

        $this->validationServiceMock->expects($this->once())
            ->method('validate')
            ->with($email, $type)
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_HOODOOR_MAX_TIME_EXPIRATION->value)
            ->willReturn($maxTimeExpiration);

        $this->randomMock->expects($this->once())
            ->method('getUniqueHash')
            ->willReturn($uniqueHash);

        $loginRequestMock = $this->createMock(LoginRequest::class);

        $this->loginRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($loginRequestMock);

        $loginRequestMock->expects($this->once())->method('setEmail')->with($email)->willReturnSelf();
        $loginRequestMock->expects($this->once())->method('setType')->with($type)->willReturnSelf();
        $loginRequestMock->expects($this->once())->method('setToken')->with($uniqueHash)->willReturnSelf();
        $loginRequestMock->expects($this->once())->method('setExpiresAt')->with($this->isInstanceOf(DateTime::class))->willReturnSelf();

        $this->loginRequestRepositoryMock->expects($this->once())
            ->method('save')
            ->with($loginRequestMock);

        $this->assertTrue($this->requestService->create($email, $type));
    }

    public function testCreateReturnsFalseWhenValidationFails()
    {
        $email = 'invalid@magento.test';
        $type = 'login';

        $this->validationServiceMock->expects($this->once())
            ->method('validate')
            ->with($email, $type)
            ->willReturn(false);

        $this->scopeConfigMock->expects($this->never())->method('getValue');
        $this->loginRequestFactoryMock->expects($this->never())->method('create');
        $this->loginRequestRepositoryMock->expects($this->never())->method('save');

        $this->assertFalse($this->requestService->create($email, $type));
    }

    public function testCreateReturnsFalseAndLogsErrorWhenExceptionIsThrown()
    {
        $email = 'customer@magento.test';
        $type = 'login';
        $exceptionMessage = 'Something went wrong while processing your request.';

        $this->validationServiceMock->expects($this->once())
            ->method('validate')
            ->with($email, $type)
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willThrowException(new RequestException($exceptionMessage));

        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with($exceptionMessage);

        $this->assertFalse($this->requestService->create($email, $type));
    }
}
