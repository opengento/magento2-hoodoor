<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Pwl;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect as RedirectAlias;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Exception\RequestException;
use Opengento\Hoodoor\Service\Customer\Login as LoginService;
use Opengento\Hoodoor\Service\JwtManager;

class ProcessLogin implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestLoginRepositoryInterface $loginRequestRepository,
        private readonly LoginService $loginService,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly MessageManager $messageManager,
        private readonly JwtManager $jwtManager
    ) {
    }

    public function execute(): ResponseInterface|RedirectAlias // phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
    {
        $redirect = $this->redirectFactory->create();
        $params = $this->request->getParams();
        if ($params) {
            try {
                if (isset($params['request'])) {
                    $decryptedData = $this->jwtManager->validateToken($params['request']);
                    if ($decryptedData) {
                        $params = [
                            'email' => $decryptedData->email,
                            'token' => $decryptedData->token
                        ];
                    }
                    if (isset($params['email']) && isset($params['token'])) {
                        try {
                            $request = $this->loginRequestRepository->get($params['email']);
                            if ($request->getToken() === $params['token']) {
                                if ($request->hasBeenUsed() || $request->hasExpired()) {
                                    $this->messageManager->addErrorMessage(
                                        __('Unable to execute request. Please try again.')
                                    );
                                    return $redirect->setPath('*/account/login');
                                }
                                $this->loginRequestRepository->lock($request);
                                $this->loginService->perform($params);
                                $this->loginRequestRepository->delete($request);
                            }
                        } catch (NoSuchEntityException) {
                            throw new RequestException(_('Invalid request. Please try again.'));
                        }
                    } else {
                        throw new RequestException(_('Invalid request. Please try again.'));
                    }
                } else {
                    throw new RequestException(_('Invalid request. Please try again.'));
                }
            } catch (RequestException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*/account');
    }
}
