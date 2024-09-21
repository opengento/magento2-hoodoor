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
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Exception\RequestException;
use Opengento\Hoodoor\Service\Customer\Login as LoginService;
use Opengento\Hoodoor\Service\Request\Encryption as EncryptionService;

class ProcessLogin implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestLoginRepositoryInterface $loginRequestRepository,
        private readonly LoginService $loginService,
        private readonly EncryptionService $encryptionService,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly MessageManager $messageManager
    ) {
    }

    public function execute(): ResponseInterface|RedirectAlias // phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
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
                        } else {
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
