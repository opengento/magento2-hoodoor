<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\AdobeImsReAuth\Block\User\Role\Tab;

use Magento\User\Block\Role\Tab\Info;
use Opengento\Hoodoor\Service\Admin\PasswordVerification;

class RemoveReAuthVerification
{
    public function __construct(
        private readonly PasswordVerification $passwordVerification,
    )
    {
    }

    public function beforeGetFormHtml(Info $subject): void
    {
        $this->passwordVerification->remove($subject);
    }
}
