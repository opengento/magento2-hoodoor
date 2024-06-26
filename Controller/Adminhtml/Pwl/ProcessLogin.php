<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Adminhtml\Pwl;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Exception\RequestException;
use Opengento\Hoodoor\Service\Admin\Login as LoginService;
use Opengento\Hoodoor\Service\Request\Encryption as EncryptionService;

class ProcessLogin implements HttpGetActionInterface
{
    /**
     * @param \Opengento\Hoodoor\Api\RequestLoginRepositoryInterface $loginRequestRepository
     * @param \Opengento\Hoodoor\Service\Admin\Login $adminLoginService
     * @param \Opengento\Hoodoor\Service\Request\Encryption $encryptionService
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected readonly RequestLoginRepositoryInterface $loginRequestRepository,
        protected readonly LoginService $adminLoginService,
        protected readonly EncryptionService $encryptionService,
        protected readonly RequestInterface $request,
        protected readonly RedirectFactory $redirectFactory,
        protected readonly MessageManager $messageManager,
        protected readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirect = $this->redirectFactory->create();
        $params = $this->request->getParams();
        if ($params) {
            try {
                if (isset($params['request'])) {
                    $secretKey = $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value);
                    $decryptedData = $this->encryptionService->decrypt($params['request'], $secretKey);
                    $params = explode("/", $decryptedData);
                    $params = array_chunk($params, 2);
                    $params = array_combine(array_column($params, 0), array_column($params, 1));
                    if (isset($params['email']) && isset($params['token'])) {
                        $loginRequest = $this->loginRequestRepository->get($params['email']);
                        if ($loginRequest->getToken() === $params['token']) {
                            if ($loginRequest->hasBeenUsed() || $loginRequest->hasExpired()) {
                                $this->messageManager->addErrorMessage(__('Unable to execute request. Please try again.'));
                                return $redirect->setPath('*');
                            }
                            $this->loginRequestRepository->lock($loginRequest);
                            $this->request->setParams(['email' => $params['email']]);
                            $this->adminLoginService->perform($this->request);
                            $this->loginRequestRepository->delete($loginRequest);
                        } else {
                            throw new RequestException(_('Invalid request. Please try again.'));
                        }
                    } else {
                        throw new RequestException(_('Invalid request. Please try again.'));
                    }
                } else {
                    throw new RequestException(_('Invalid request. Please try again.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*');
    }
}
