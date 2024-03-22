<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Processor;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface;
use Opengento\PasswordLessLogin\Model\Email;
use Opengento\PasswordLessLogin\Model\LoginRequest;
use Psr\Log\LoggerInterface;

class EmailProcessor
{
    /**
     * @param \Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface $loginRequestRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected readonly RequestLoginRepositoryInterface $loginRequestRepository,
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly TransportBuilder $transportBuilder,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly StateInterface $inlineTranslation,
        protected readonly UrlInterface $url,
        protected readonly EncryptorInterface $encryptor,
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $to
     * @return void
     */
    public function sendMail(string $to): void
    {
        try {

            $templateId = $this->scopeConfig->getValue(Email::XML_PATH_PASSWORDLESSLOGIN_TEMPLATE_ID);
            $fromEmail = $this->scopeConfig->getValue(Email::XML_PATH_PASSWORDLESSLOGIN_SENDER_EMAIL);
            $fromName = $this->scopeConfig->getValue(Email::XML_PATH_PASSWORDLESSLOGIN_SENDER_NAME);

            $templateVars = [
                'request' => $this->getAccountDataByEmail($to)
            ];

            $storeId = $this->storeManager->getStore()->getId();

            $this->inlineTranslation->suspend();

            $storeScope = ScopeInterface::SCOPE_STORE;

            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope(
                    [
                        'email' => $fromEmail,
                        'name' => $fromName
                    ],
                    $storeScope
                )
                ->addTo($to)
                ->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();

        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAccountDataByEmail(string $email): LoginRequest
    {
        return $this->loginRequestRepository->get($email);
    }
}
