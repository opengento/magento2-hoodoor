<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Model\LoginRequestFactory;
use Opengento\Hoodoor\Model\LoginRequestRepository;
use Psr\Log\LoggerInterface;

class Request
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly LoginRequestFactory $loginRequestFactory,
        private readonly LoginRequestRepository $loginRequestRepository,
        private readonly Validation $validationService,
        private readonly Random $random,
        private readonly LoggerInterface $logger
    ) {
    }

    public function create(string $email, string $type): bool
    {
        try {
            $isValid = $this->validationService->validate($email, $type);
            if ($isValid) {
                $dateTime = new \DateTime();
                $maxTimeExpiration = $this->scopeConfig
                    ->getValue(Config::XML_PATH_HOODOOR_MAX_TIME_EXPIRATION->value);
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
