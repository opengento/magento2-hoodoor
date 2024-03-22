<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Service;

use Opengento\PasswordLessLogin\Model\LoginRequestFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Model\LoginRequestRepository;
use Opengento\PasswordLessLogin\Processor\EmailProcessor;
use Opengento\PasswordLessLogin\Service\Account\Validation as AccountValidation;

class Account
{
    /**
     * @param \Opengento\PasswordLessLogin\Model\LoginRequestFactory $loginRequestFactory
     * @param \Opengento\PasswordLessLogin\Model\LoginRequestRepository $loginRequestRepository
     * @param \Opengento\PasswordLessLogin\Service\Account\Validation $accountValidation
     * @param \Opengento\PasswordLessLogin\Processor\EmailProcessor $emailProcessor
     * @param \Magento\Framework\Message\Manager $messageManager
     */
    public function __construct(
        protected readonly LoginRequestFactory $loginRequestFactory,
        protected readonly LoginRequestRepository $loginRequestRepository,
        protected readonly AccountValidation $accountValidation,
        protected readonly EmailProcessor $emailProcessor,
        protected readonly MessageManager $messageManager
    ) {
    }

    /**
     * @param string $email
     * @return void
     */
    public function sendLoginEmail(string $email): void
    {
        $this->emailProcessor->sendMail($email);
    }

    /**
     * @param string $email
     * @return false
     */
    public function createLoginRequest(string $email): bool
    {
        try {
            $isValid = $this->accountValidation->validate($email);
            if ($isValid) {
                $dateTime = new \DateTime();
                $expiresAt = $dateTime->modify('+15 minutes');
                $mathRandom = new Random();
                $token = $mathRandom->getRandomString(64);
                $loginRequest = $this->loginRequestFactory->create();
                $loginRequest->setEmail($email)
                    ->setToken($token)
                    ->setExpiresAt($expiresAt);
                $this->loginRequestRepository->save($loginRequest);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
        return true;
    }
}
