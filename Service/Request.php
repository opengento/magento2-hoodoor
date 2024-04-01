<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Enum\Config;
use Opengento\PasswordLessLogin\Model\LoginRequestFactory;
use Opengento\PasswordLessLogin\Model\LoginRequestRepository;
use Opengento\PasswordLessLogin\Processor\EmailProcessor;
use Psr\Log\LoggerInterface;

class Request
{
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Opengento\PasswordLessLogin\Model\LoginRequestFactory $loginRequestFactory
     * @param \Opengento\PasswordLessLogin\Model\LoginRequestRepository $loginRequestRepository
     * @param \Opengento\PasswordLessLogin\Service\Validation $validationService
     * @param \Opengento\PasswordLessLogin\Processor\EmailProcessor $emailProcessor
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly LoginRequestFactory $loginRequestFactory,
        protected readonly LoginRequestRepository $loginRequestRepository,
        protected readonly Validation $validationService,
        protected readonly EmailProcessor $emailProcessor,
        protected readonly MessageManager $messageManager,
        protected readonly Random $random,
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $email
     * @param string $type
     * @return false
     */
    public function create(string $email, string $type): bool
    {
        try {
            $isValid = $this->validationService->validate($email, $type);
            if ($isValid) {
                $dateTime = new \DateTime();
                $maxTimeExpiration = $this->scopeConfig
                    ->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_MAX_TIME_EXPIRATION->value);
                $loginRequest = $this->loginRequestFactory->create();
                $loginRequest->setEmail($email)
                    ->setType($type)
                    ->setToken($this->random->getUniqueHash())
                    ->setExpiresAt($dateTime->modify($maxTimeExpiration));
                $this->loginRequestRepository->save($loginRequest);
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return false;
    }
}
