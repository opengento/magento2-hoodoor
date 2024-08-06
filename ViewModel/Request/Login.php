<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\ViewModel\Request;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface as FrontendUrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Login implements ArgumentInterface
{
    /**
     * Construct
     *
     * @param \Magento\Framework\UrlInterface $urlFrontend
     * @param \Magento\Backend\Model\UrlInterface $urlBackend
     * @param \Magento\Framework\App\State $state
     */
    public function __construct( //phpcs:ignore
        protected readonly FrontendUrlInterface $urlFrontend,
        protected readonly BackendUrlInterface $urlBackend,
        protected readonly State $state
    ) {
    }

    /**
     * Get Post Action Url
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostActionUrl(): string
    {
        $areaCode = $this->state->getAreaCode();

        if ($areaCode === Area::AREA_ADMINHTML) {
            return $this->urlBackend->getUrl('admin/pwl/requestlogin');
        }

        return $this->urlFrontend->getUrl('customer/pwl/requestlogin');
    }
}
