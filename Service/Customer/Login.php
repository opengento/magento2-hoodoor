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

class Login
{
    private $cookieMetadataManager;

    private $cookieMetadataFactory;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Customer\Model\Customer\CredentialsValidator $credentialsValidator
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Customer\Api\SessionCleanerInterface $sessionCleaner
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly AccountManagementInterface $customerAccountManagement,
        protected readonly CredentialsValidator $credentialsValidator,
        protected readonly CustomerRegistry $customerRegistry,
        protected readonly Encryptor $encryptor,
        protected readonly SessionCleanerInterface $sessionCleaner,
        protected readonly Session $session
    ) {
    }

    /**
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function perform(array $data): void
    {
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
    }

    /**
     * @param $password
     * @return string
     */
    private function createPasswordHash($password): string
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager|mixed
     */
    private function getCookieManager(): mixed
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory|mixed
     */
    private function getCookieMetadataFactory(): mixed
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
}
