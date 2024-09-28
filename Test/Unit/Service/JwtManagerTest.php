<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service;

use Firebase\JWT\Key;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Helper\JwtHelper;
use Opengento\Hoodoor\Service\JwtManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class JwtManagerTest extends TestCase
{
    private JwtHelper|MockObject $jwtHelperMock;

    private ScopeConfigInterface|MockObject $scopeConfigMock;

    private LoggerInterface|MockObject $loggerMock;

    private JwtManager $jwtManager;

    private string $secretKey = 'test_secret_key';

    protected function setUp(): void
    {
        $this->jwtHelperMock = $this->createMock(JwtHelper::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_HOODOOR_SECRET_KEY->value)
            ->willReturn($this->secretKey);

        $this->jwtManager = new JwtManager(
            $this->jwtHelperMock,
            $this->scopeConfigMock,
            $this->loggerMock
        );
    }

    public function testGenerateToken()
    {
        $payload = ['user_id' => 1];
        $expirationInSeconds = 3600;
        $issuedAt = time();
        $expectedExpire = $issuedAt + $expirationInSeconds;

        $expectedTokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expectedExpire,
        ]);

        $this->jwtHelperMock->expects($this->once())
            ->method('encode')
            ->with($expectedTokenPayload, $this->secretKey, 'HS256')
            ->willReturn('generated_token');

        $generatedToken = $this->jwtManager->generateToken($payload, $expirationInSeconds);

        $this->assertEquals('generated_token', $generatedToken);
    }

    public function testValidateTokenReturnsDecodedPayload()
    {
        $token = 'valid_token';
        $decodedPayload = (object) ['user_id' => 1, 'iat' => time(), 'exp' => time() + 3600];

        $this->jwtHelperMock->expects($this->once())
            ->method('decode')
            ->with($token, new Key($this->secretKey, 'HS256'))
            ->willReturn($decodedPayload);

        $this->assertEquals($decodedPayload, $this->jwtManager->validateToken($token));
    }

    public function testValidateTokenReturnsFalseOnInvalidToken()
    {
        $token = 'invalid_token';

        $this->jwtHelperMock->expects($this->once())
            ->method('decode')
            ->with($token, new Key($this->secretKey, 'HS256'))
            ->willThrowException(new \Exception('Invalid token'));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Invalid token');

        $this->assertFalse($this->jwtManager->validateToken($token));
    }
}
