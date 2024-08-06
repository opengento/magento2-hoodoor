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
use Magento\Framework\Message\Manager;
use Magento\Framework\UrlInterface;
use Opengento\Hoodoor\Model\Admin\User;
use Psr\Log\LoggerInterface;

class Login
{
    /**
     * @param \Opengento\Hoodoor\Model\Admin\User $user
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Framework\Message\Manager $messageManager
     */
    public function __construct( //phpcs:ignore
        protected readonly User $user,
        protected readonly Random $random,
        protected readonly UrlInterface $url,
        protected readonly ResponseInterface $response,
        protected readonly ActionFlag $actionFlag,
        protected readonly LoggerInterface $logger,
        protected readonly Auth $auth,
        protected readonly Manager $messageManager
    ) {
    }

    /**
     * Perform Admin Login
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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

    /**
     * Get Backend User
     *
     * @param array $data
     * @return \Opengento\Hoodoor\Model\Admin\User|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getBackendUser(array $data): ?User
    {
        $user = $this->user->loadByEmail($data['email']);
        try {
            if ($user->getId()) {
                return $user;
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return null;
    }

    /**
     * Process Not LoggedIn User
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    protected function processNotLoggedInUser(RequestInterface $request): void
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

    /**
     * Perform Login
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    protected function performLogin(RequestInterface $request): bool
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

    /**
     * Redirect If Needed After Login
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    protected function redirectIfNeededAfterLogin(RequestInterface $request): bool
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
