<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Controller\Account;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface;
use Opengento\PasswordLessLogin\Enum\Config;
use Opengento\PasswordLessLogin\Exception\RequestException;
use Opengento\PasswordLessLogin\Service\Account\Login as LoginService;
use Opengento\PasswordLessLogin\Service\Request\Encryption as EncryptionService;

class ProcessLogin implements HttpGetActionInterface
{
    /**
     * @param \Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface $loginRequestRepository
     * @param \Opengento\PasswordLessLogin\Service\Account\Login $loginService
     * @param \Opengento\PasswordLessLogin\Service\Request\Encryption $encryptionService
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected readonly RequestLoginRepositoryInterface $loginRequestRepository,
        protected readonly LoginService $loginService,
        protected readonly EncryptionService $encryptionService,
        protected readonly RequestInterface $request,
        protected readonly RedirectFactory $redirectFactory,
        protected readonly MessageManager $messageManager,
        protected readonly ScopeConfigInterface $scopeConfig
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
                    $secretKey = $this->scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY->value);
                    $decryptedData = $this->encryptionService->decrypt($params['request'], $secretKey);
                    $params = explode("/", $decryptedData);
                    $params = array_chunk($params, 2);
                    $params = array_combine(array_column($params, 0), array_column($params, 1));
                    if (isset($params['email']) && isset($params['token'])) {
                        $request = $this->loginRequestRepository->get($params['email']);
                        if ($request->getToken() === $params['token']) {
                            if ($request->hasBeenUsed() || $request->hasExpired()) {
                                $this->messageManager->addErrorMessage(__('Unable to execute request. Please try again.'));
                                return $redirect->setPath('customer/account/login');
                            }
                            $this->loginRequestRepository->lock($request);
                            $this->loginService->process($params);
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
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('customer/account');
    }
}
