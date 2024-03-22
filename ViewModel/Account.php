<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Account implements ArgumentInterface
{
    /**
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        protected readonly UrlInterface $url
    ) {
    }

    /**
     * @return string
     */
    public function getPostActionUrl(): string
    {
        return $this->url->getUrl(
            'customer/account/requestlogin',
            [
                '_secure' => true
            ]
        );
    }
}
