<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Processor;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface;
use Opengento\PasswordLessLogin\Enum\Config;
use Opengento\PasswordLessLogin\Model\LoginRequest;
use Opengento\PasswordLessLogin\Service\Request\Encryption;
use Psr\Log\LoggerInterface;

class EmailProcessor
{
    /**
     * @var \Opengento\PasswordLessLogin\Model\LoginRequest|null
     */
    private ?LoginRequest $accountData;

    /**
     * @param \Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface $loginRequestRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\UrlInterface $url
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Opengento\PasswordLessLogin\Service\Request\Encryption $encryptionService
     */
    public function __construct(
        protected readonly RequestLoginRepositoryInterface $loginRequestRepository,
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly TransportBuilder $transportBuilder,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly StateInterface $inlineTranslation,
        protected readonly UrlInterface $url,
        protected readonly LoggerInterface $logger,
        protected readonly Encryption $encryptionService
    ) {
        $this->accountData = null;
    }

    /**
     * @param string $to
     * @return void
     */
    public function sendMail(string $to): void
    {
        try {

            $templateId = $this->scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_TEMPLATE_ID->value);
            $fromEmail = $this->scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_SENDER_EMAIL->value);
            $fromName = $this->scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_SENDER_NAME->value);

            $accountData = $this->getAccountDataByEmail($to);
            $requestEmail = $accountData->getEmail();
            $requestToken = $accountData->getToken();

            $data = sprintf('email/%s/token/%s', $requestEmail, $requestToken);
            $templateVars = [
                'request' => $this->encryptionService->encrypt(
                    $data,
                    $this->scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY->value)
                )
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
     * @param string $email
     * @return \Opengento\PasswordLessLogin\Model\LoginRequest
     */
    protected function getAccountDataByEmail(string $email): LoginRequest
    {
        if (!$this->accountData) {
            $this->accountData = $this->loginRequestRepository->get($email);
        }
        return $this->accountData;
    }
}
