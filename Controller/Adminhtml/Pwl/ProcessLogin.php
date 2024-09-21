<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Adminhtml\Pwl;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Exception\RequestException;
use Opengento\Hoodoor\Service\Admin\Login as LoginService;
use Opengento\Hoodoor\Service\Request\Encryption as EncryptionService;

class ProcessLogin implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestLoginRepositoryInterface $loginRequestRepository,
        private readonly LoginService $adminLoginService,
        private readonly EncryptionService $encryptionService,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly MessageManager $messageManager
    ) {
    }

    public function execute(): ResponseInterface|Redirect // phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
    {
        $redirect = $this->redirectFactory->create();
        $params = $this->request->getParams();
        if ($params) {
            try {
                if (isset($params['request'])) {
                    $decryptedData = $this->encryptionService->decrypt($params['request']);
                    $params = explode("/", $decryptedData);
                    $params = array_chunk($params, 2);
                    $params = array_combine(array_column($params, 0), array_column($params, 1));
                    if (isset($params['email']) && isset($params['token'])) {
                        $loginRequest = $this->loginRequestRepository->get($params['email']);
                        if ($loginRequest->getToken() === $params['token']) {
                            if ($loginRequest->hasBeenUsed() || $loginRequest->hasExpired()) {
                                $this->messageManager->addErrorMessage(
                                    __('Unable to execute the request. Please try again.')
                                );
                                return $redirect->setPath('*');
                            }
                            $this->loginRequestRepository->lock($loginRequest);
                            $this->request->setParams(['email' => $params['email']]);
                            $this->adminLoginService->perform($this->request);
                            $this->loginRequestRepository->delete($loginRequest);
                        } else {
                            throw new RequestException(
                                _('Invalid request. Please try again.')
                            );
                        }
                    } else {
                        throw new RequestException(
                            _('Invalid request. Please try again.')
                        );
                    }
                } else {
                    throw new RequestException(
                        _('Invalid request. Please try again.')
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*');
    }
}
