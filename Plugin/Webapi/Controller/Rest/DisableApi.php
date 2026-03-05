<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\Webapi\Controller\Rest;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Webapi\Controller\Rest;

class DisableApi
{
    /** Block customer-related REST API routes to prevent password-based access. */
    public function beforeDispatch(Rest $subject, RequestInterface $request)
    {
        $blockedRoutes = [
            'rest/V1/customers',
            'rest/all/V1/customers',
            'rest/default/V1/customers'
        ];

        $currentPath = $request->getPathInfo();

        foreach ($blockedRoutes as $route) {
            if (str_contains($currentPath, $route)) {
                throw new AuthorizationException(__('Access to this API is disabled.'));
            }
        }
    }
}
