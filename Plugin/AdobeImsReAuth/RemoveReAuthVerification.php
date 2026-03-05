<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\AdobeImsReAuth;

use Magento\Backend\Block\Widget\Form\Generic;
use Opengento\Hoodoor\Service\Admin\PasswordVerification;

class RemoveReAuthVerification
{
    public function __construct(
        private readonly PasswordVerification $passwordVerification,
    )
    {
    }

    /** Remove password verification fields from the admin form. */
    public function beforeGetFormHtml(Generic $subject): void
    {
        $this->passwordVerification->remove($subject);
    }
}
