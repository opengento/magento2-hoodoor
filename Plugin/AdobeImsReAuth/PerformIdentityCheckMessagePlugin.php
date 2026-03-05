<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\AdobeImsReAuth;

use Magento\AdminAdobeIms\Service\ImsConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\User\Model\User;

class PerformIdentityCheckMessagePlugin
{
    private const string XML_PATH_HOODOOR_ENABLE_ADMIN = 'hoodoor/general/enable_admin';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ImsConfig $adminImsConfig,
    )
    {
    }

    /**
     * @throws AuthenticationException
     */
    /** Skip identity verification when Hoodoor admin is enabled, or wrap IMS re-auth errors. */
    public function aroundPerformIdentityCheck(User $subject, callable $proceed, ?string $passwordString)
    {
        $hoodoorAdminIsEnable = $this->scopeConfig->isSetFlag(self::XML_PATH_HOODOOR_ENABLE_ADMIN);
        if ($hoodoorAdminIsEnable) {
            return true;
        }

        if ($this->adminImsConfig->enabled() === false) {
            return $proceed($passwordString);
        }

        try {
            return $proceed($passwordString);
        } catch (AuthenticationException) {
            throw new AuthenticationException(
                __('Please perform the AdobeIms reAuth and try again.')
            );
        }
    }
}
