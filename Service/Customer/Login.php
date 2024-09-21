<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Psr\Log\LoggerInterface;

class Login
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly AccountManagementInterface $customerAccountManagement,
        private readonly CredentialsValidator $credentialsValidator,
        private readonly CustomerRegistry $customerRegistry,
        private readonly Encryptor $encryptor,
        private readonly SessionCleanerInterface $sessionCleaner,
        private readonly Session $session,
        private readonly CookieMetadataFactory $cookieMetadataFactory,
        private readonly PhpCookieManager $cookieMetadataManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function perform(array $data): bool
    {
        try {
            // Set new password each time you need to login
            $customer = $this->customerRepository->get($data['email']);
            $this->credentialsValidator->checkPasswordDifferentFromEmail($data['email'], $data['token']);
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerSecure->setPasswordHash($this->createPasswordHash($data['token']));
            $this->sessionCleaner->clearFor((int)$customer->getId());
            $this->customerRepository->save($customer);
            // Now login
            $customer = $this->customerAccountManagement->authenticate($data['email'], $data['token']);
            $this->session->setCustomerDataAsLoggedIn($customer);
            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
            return true;
        } catch (InputException|InputMismatchException|LocalizedException $e) {
            $this->logger->debug($e->getMessage());
        }
        return false;
    }

    private function createPasswordHash(string $password): string
    {
        return $this->encryptor->getHash($password, true);
    }

    private function getCookieManager(): PhpCookieManager
    {
        return $this->cookieMetadataManager;
    }

    private function getCookieMetadataFactory(): CookieMetadataFactory
    {
        return $this->cookieMetadataFactory;
    }
}
