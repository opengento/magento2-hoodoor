<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service;

use Magento\Framework\Math\Random;
use Opengento\Hoodoor\Service\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    private Random|MockObject $randomMock;

    private Password $passwordService;

    protected function setUp(): void
    {
        $this->randomMock = $this->createMock(Random::class);

        $this->passwordService = new Password($this->randomMock);
    }

    public function testGenerateReturnsUniqueHash()
    {
        $this->randomMock
            ->expects($this->once())
            ->method('getUniqueHash')
            ->willReturn('uniqueRandomHash');

        $this->assertEquals('uniqueRandomHash', $this->passwordService->generate());
    }
}
