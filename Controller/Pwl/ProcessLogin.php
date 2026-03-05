<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Pwl;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Service\Customer\Login as LoginService;
use Opengento\Hoodoor\Service\JwtManager;

class ProcessLogin implements HttpGetActionInterface
{
    use ProcessLoginTrait;

    public function __construct(
        private readonly RequestLoginRepositoryInterface $loginRequestRepository,
        private readonly LoginService $loginService,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly MessageManager $messageManager,
        private readonly JwtManager $jwtManager
    ) {
    }

    protected function performLogin(array $params): void
    {
        $this->loginService->perform($params);
    }

    protected function getDefaultRedirectPath(): string
    {
        return '*/account';
    }

    protected function getErrorRedirectPath(): string
    {
        return '*/account/login';
    }
}
