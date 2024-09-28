<?php

namespace Opengento\Hoodoor\Test\Unit\Plugin\Webapi\Controller\Rest;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Webapi\Controller\Rest;
use Opengento\Hoodoor\Plugin\Webapi\Controller\Rest\DisableApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DisableApiTest extends TestCase
{
    private DisableApi $plugin;

    private Rest|MockObject $restControllerMock;

    protected function setUp(): void
    {
        $this->plugin = new DisableApi();
        $this->restControllerMock = $this->createMock(Rest::class);
    }

    public function testBeforeDispatchThrowsAuthorizationExceptionForBlockedRoutes()
    {
        $blockedRoutes = [
            'https://magento.test/rest/V1/customers',
            'https://magento.test/rest/all/V1/customers',
            'https://magento.test/rest/default/V1/customers'
        ];

        foreach ($blockedRoutes as $blockedRoute) {
            $requestMock = $this->getMockBuilder(HttpRequest::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['getPathInfo'])
                ->getMock();

            $requestMock->expects($this->once())
                ->method('getPathInfo')
                ->willReturn($blockedRoute);

            $this->expectException(AuthorizationException::class);
            $this->expectExceptionMessage('Access to this API is disabled.');

            $this->plugin->beforeDispatch($this->restControllerMock, $requestMock);
        }
    }

    public function testBeforeDispatchDoesNotThrowExceptionForAllowedRoutes()
    {
        $allowedRoutes = [
            'https://magento.test/one/allowed/route',
            'https://magento.test/any/allowed/route',
            'https://magento.test/another/allowed/route'
        ];

        foreach ($allowedRoutes as $allowedRoute) {
            $requestMock = $this->getMockBuilder(HttpRequest::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['getPathInfo'])
                ->getMock();

            $requestMock->expects($this->once())
                ->method('getPathInfo')
                ->willReturn($allowedRoute);

            $this->plugin->beforeDispatch($this->restControllerMock, $requestMock);
            $this->addToAssertionCount(1);
        }
    }
}
