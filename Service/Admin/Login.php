<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Admin;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\UrlInterface;
use Opengento\Hoodoor\Model\Admin\User;
use Psr\Log\LoggerInterface;

class Login
{
    public function __construct(
        private readonly User $user,
        private readonly Random $random,
        private readonly UrlInterface $url,
        private readonly ResponseInterface $response,
        private readonly ActionFlag $actionFlag,
        private readonly LoggerInterface $logger,
        private readonly Auth $auth,
        private readonly MessageManager $messageManager
    ) {
    }

    public function perform(RequestInterface $request): void
    {
        $params = $request->getParams();
        $backendUser = $this->getBackendUser($params);
        if ($backendUser) {
            $password = $this->random->getUniqueHash();
            // Set new password each time you need to login
            $backendUser->setPassword($password);
            $backendUser->save();
            $request = $request->setPostValue('login', [
                'username' => $backendUser->getUserName(),
                'password' => $password
            ]);
            // Now login
            $this->processNotLoggedInUser($request);
        }
    }

    private function getBackendUser(array $data): ?User
    {
        try {
            $user = $this->user->loadByEmail($data['email']);
            if ($user->getId()) {
                return $user;
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return null;
    }

    private function processNotLoggedInUser(RequestInterface $request): void
    {
        $isRedirectNeeded = false;
        if ($request->getPost('login')) {
            if ($this->performLogin($request)) {
                $isRedirectNeeded = $this->redirectIfNeededAfterLogin($request);
            }
        }
        if (!$isRedirectNeeded && !$request->isForwarded()) {
            if ($request->getParam('isIframe')) {
                $request->setForwarded(true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('deniedIframe')
                    ->setDispatched(false);
            } elseif ($request->getParam('isAjax')) {
                $request->setForwarded(true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('deniedJson')
                    ->setDispatched(false);
            } else {
                $request->setForwarded(true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('auth')
                    ->setActionName('login')
                    ->setDispatched(false);
            }
        }
    }

    private function performLogin(RequestInterface $request): bool
    {
        $outputValue = true;
        $postLogin = $request->getPost('login');
        $username = $postLogin['username'] ?? '';
        $password = $postLogin['password'] ?? '';
        $request->setPostValue('login', null);

        try {
            $this->auth->login($username, $password);
        } catch (AuthenticationException $e) {
            if (!$request->getParam('messageSent')) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $request->setParam('messageSent', true);
                $outputValue = false;
            }
        }
        return $outputValue;
    }

    private function redirectIfNeededAfterLogin(RequestInterface $request): bool
    {
        $requestUri = null;

        // Checks, whether secret key is required for admin access or request uri is explicitly set
        if ($this->url->useSecretKey()) {
            // The requested URL has an invalid secret key and therefore redirecting to this URL
            // will cause a security vulnerability.
            $requestUri = $this->url->getUrl($this->url->getStartupPageUrl());
        } elseif ($request) {
            $requestUri = $request->getRequestUri();
        }

        if (!$requestUri) {
            return false;
        }

        $this->response->setRedirect($requestUri);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        return true;
    }
}
