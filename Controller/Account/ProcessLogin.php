<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Controller\Account;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Exception\LoginException;
use Opengento\PasswordLessLogin\Model\LoginRequest;
use Opengento\PasswordLessLogin\Model\LoginRequestRepository;
use Opengento\PasswordLessLogin\Service\Account\Login as LoginService;

class ProcessLogin implements HttpGetActionInterface
{
    /**
     * @var \Opengento\PasswordLessLogin\Model\LoginRequest|null
     */
    private ?LoginRequest $loginRequest;

    /**
     * @param \Opengento\PasswordLessLogin\Model\LoginRequestRepository $loginRequestRepository
     * @param \Opengento\PasswordLessLogin\Service\Account\Login $loginService
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Message\Manager $messageManager
     */
    public function __construct(
        protected readonly LoginRequestRepository $loginRequestRepository,
        protected readonly LoginService $loginService,
        protected readonly RequestInterface $request,
        protected readonly RedirectFactory $redirectFactory,
        protected readonly MessageManager $messageManager
    ) {
        $this->loginRequest = null;
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
                if (isset($params['email']) && isset($params['token'])) {
                    $this->setLoginRequest($params['email']);
                } else {
                    throw new LoginException(_('Invalid request. Please try again.'));
                }
                if(!$this->getLoginRequest()->isValidToken($params['token'])) {
                    throw new LoginException(_('Invalid request. Please try again.'));
                }
                if ($this->getLoginRequest()->hasBeenUsed() || $this->getLoginRequest()->hasExpired()) {
                    $this->messageManager->addErrorMessage(__('Unable to execute request. Please try again.'));
                    return $redirect->setPath('customer/account/login');
                }
                $this->lockRequest();
                $this->loginService->process($params);
                $this->deleteRequest();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('customer/account');
    }

    /**
     * @param string $email
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setLoginRequest(string $email): void
    {
        if (!$this->loginRequest) {
            $this->loginRequest = $this->loginRequestRepository->get($email);
        }
    }

    /**
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest|null
     */
    public function getLoginRequest(): ?LoginRequest
    {
        return $this->loginRequest;
    }

    /**
     * @throws \Exception
     */
    protected function lockRequest(): void
    {
        $request = $this->getLoginRequest();
        $this->loginRequestRepository->lock($request);
    }

    /**
     * @throws \Exception
     */
    protected function deleteRequest(): void
    {
        $request = $this->getLoginRequest();
        $this->loginRequestRepository->delete($request);
    }
}
