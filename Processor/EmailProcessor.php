<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Processor;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Email\Model\BackendTemplate;
use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Model\LoginRequest;
use Opengento\Hoodoor\Service\JwtManager;
use Psr\Log\LoggerInterface;

class EmailProcessor
{
    private ?LoginRequest $accountData;

    public function __construct(
        private readonly RequestLoginRepositoryInterface $loginRequestRepository,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly TransportBuilder $transportBuilder,
        private readonly StateInterface $inlineTranslation,
        private readonly LoggerInterface $logger,
        private readonly JwtManager $jwtManager,
        private readonly UrlInterface $frontendUrl,
        private readonly BackendUrlInterface $backendUrl
    ) {
        $this->accountData = null;
    }

    /** Build and send the password-less login email with a JWT link. */
    public function sendMail(string $to, string $type): void
    {
        try {
            $templateId = $type === 'admin'
                ? 'admin_login'
                : $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_TEMPLATE_ID->value);
            $fromEmail = $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_SENDER_EMAIL->value);
            $fromName = $this->scopeConfig->getValue(Config::XML_PATH_HOODOOR_SENDER_NAME->value);

            $accountData = $this->getAccountDataByEmail($to);
            $requestEmail = $accountData->getEmail();
            $requestToken = $accountData->getToken();

            $jwtToken = $this->jwtManager->generateToken([
                'email' => $requestEmail,
                'token' => $requestToken
            ], 900);

            $loginUrl = $type === 'admin'
                ? $this->backendUrl->getUrl('adminhtml/pwl/processlogin', ['request' => $jwtToken, '_nosid' => true])
                : $this->frontendUrl->getUrl('customer/pwl/processlogin', ['_query' => ['request' => $jwtToken], '_nosid' => true]);

            $templateVars = [
                'type' => $type,
                'request' => $jwtToken,
                'login_url' => $loginUrl
            ];

            $this->inlineTranslation->suspend();

            $storeScope = ScopeInterface::SCOPE_STORE;

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateModel($type === 'admin' ? BackendTemplate::class : Template::class)
                ->setTemplateOptions([
                    'area' => $type === 'admin' ? Area::AREA_ADMINHTML : Area::AREA_FRONTEND,
                    'store' => Store::DEFAULT_STORE_ID
                ])
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

    /** Retrieve the login request entity for the given email (cached per instance). */
    private function getAccountDataByEmail(string $email): LoginRequest
    {
        if (!$this->accountData) {
            $this->accountData = $this->loginRequestRepository->get($email);
        }
        return $this->accountData;
    }
}
