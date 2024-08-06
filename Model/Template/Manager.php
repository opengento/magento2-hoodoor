<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Model\Template;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\Hoodoor\Enum\Config;

class Manager
{
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    /**
     * Get Template
     *
     * @param string $default
     * @param string $override
     * @param string $type
     * @return string
     */
    public function getTemplate(string $default, string $override, string $type): string
    {
        $enable = $this->scopeConfig->isSetFlag(Config::XML_PATH_HOODOOR_ENABLE_FRONTEND->value);
        if ($type === 'admin') {
            $enable = $this->scopeConfig->isSetFlag(Config::XML_PATH_HOODOOR_ENABLE_ADMIN->value);
        }
        return $enable ? $override : $default;
    }
}
