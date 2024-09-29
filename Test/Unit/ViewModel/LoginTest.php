<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Test\Unit\ViewModel;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface as FrontendUrlInterface;
use Opengento\Hoodoor\ViewModel\Request\Login;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private BackendUrlInterface|MockObject $urlBackendMock;

    private FrontendUrlInterface|MockObject $urlFrontendMock;

    private State|MockObject $stateMock;

    private Login $loginViewModel;

    protected function setUp(): void
    {
        $this->urlBackendMock = $this->createMock(BackendUrlInterface::class);
        $this->urlFrontendMock = $this->createMock(FrontendUrlInterface::class);
        $this->stateMock = $this->createMock(State::class);

        $this->loginViewModel = new Login(
            $this->urlFrontendMock,
            $this->urlBackendMock,
            $this->stateMock
        );
    }

    public function testGetPostActionUrlReturnsAdminUrlIfAdminArea()
    {
        $this->stateMock
            ->expects($this->once())
            ->method('getAreaCode')
            ->willReturn(Area::AREA_ADMINHTML);

        $this->urlBackendMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('admin/pwl/requestlogin')
            ->willReturn('https://magento.test/admin/pwl/requestlogin');

        $this->assertEquals(
            'https://magento.test/admin/pwl/requestlogin',
            $this->loginViewModel->getPostActionUrl()
        );
    }

    public function testGetPostActionUrlReturnsFrontendUrlIfFrontendArea()
    {
        $this->stateMock
            ->expects($this->once())
            ->method('getAreaCode')
            ->willReturn(Area::AREA_FRONTEND);

        $this->urlFrontendMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('customer/pwl/requestlogin')
            ->willReturn('https://magento.test/customer/pwl/requestlogin');

        $this->assertEquals(
            'https://magento.test/customer/pwl/requestlogin',
            $this->loginViewModel->getPostActionUrl()
        );
    }
}
