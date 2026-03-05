<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Pwl;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Hoodoor\Exception\RequestException;

trait ProcessLoginTrait
{
    /** Validate the JWT token from the request and perform login. */
    #[\Override]
    public function execute(): ResponseInterface|Redirect // phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
    {
        $redirect = $this->redirectFactory->create();
        $params = $this->request->getParams();
        if ($params) {
            try {
                if (!isset($params['request'])) {
                    throw new RequestException(__('Invalid request. Please try again.'));
                }
                $decryptedData = $this->jwtManager->validateToken($params['request']);
                if ($decryptedData) {
                    $params = [
                        'email' => $decryptedData->email,
                        'token' => $decryptedData->token
                    ];
                }
                if (!isset($params['email']) || !isset($params['token'])) {
                    throw new RequestException(__('Invalid request. Please try again.'));
                }
                try {
                    $loginRequest = $this->loginRequestRepository->get($params['email']);
                    if ($loginRequest->getToken() === $params['token']) {
                        if ($loginRequest->hasBeenUsed() || $loginRequest->hasExpired()) {
                            $this->messageManager->addErrorMessage(
                                __('Unable to execute request. Please try again.')
                            );
                            return $redirect->setPath($this->getErrorRedirectPath());
                        }
                        $this->loginRequestRepository->lock($loginRequest);
                        $this->performLogin($params);
                        $this->loginRequestRepository->delete($loginRequest);
                    }
                } catch (NoSuchEntityException) {
                    throw new RequestException(__('Invalid request. Please try again.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath($this->getDefaultRedirectPath());
    }

    abstract protected function performLogin(array $params): void;

    abstract protected function getDefaultRedirectPath(): string;

    abstract protected function getErrorRedirectPath(): string;
}
