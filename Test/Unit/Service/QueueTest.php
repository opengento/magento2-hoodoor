<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\Service;

use Opengento\Hoodoor\Processor\EmailProcessor;
use Opengento\Hoodoor\Service\Queue;
use Opengento\Hoodoor\Service\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    private Request|MockObject $requestServiceMock;

    private EmailProcessor|MockObject $emailProcessorMock;

    private Queue $queueService;

    protected function setUp(): void
    {
        $this->requestServiceMock = $this->createMock(Request::class);
        $this->emailProcessorMock = $this->createMock(EmailProcessor::class);

        $this->queueService = new Queue(
            $this->requestServiceMock,
            $this->emailProcessorMock
        );
    }

    public function testAddReturnsTrueWhenRequestCreationSucceeds()
    {
        $params = [
            'login' => [
                'username' => 'test@magento.test'
            ]
        ];

        $type = 'login';

        $this->requestServiceMock->expects($this->once())
            ->method('create')
            ->with('test@magento.test', $type)
            ->willReturn(true);

        $this->emailProcessorMock->expects($this->once())
            ->method('sendMail')
            ->with('test@magento.test', $type);

        $this->assertTrue($this->queueService->add($params, $type));
    }

    public function testAddReturnsFalseWhenRequestCreationFails()
    {
        $params = [
            'login' => [
                'username' => 'test@magento.test'
            ]
        ];
        $type = 'login';

        $this->requestServiceMock->expects($this->once())
            ->method('create')
            ->with('test@magento.test', $type)
            ->willReturn(false);

        $this->emailProcessorMock->expects($this->never())
            ->method('sendMail');

        $this->assertFalse($this->queueService->add($params, $type));
    }
}
