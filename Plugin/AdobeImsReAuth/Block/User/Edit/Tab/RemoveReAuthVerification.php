<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\AdobeImsReAuth\Block\User\Edit\Tab;

use Magento\User\Block\User\Edit\Tab\Main;
use Opengento\Hoodoor\Service\Admin\PasswordVerification;

class RemoveReAuthVerification
{
    public function __construct(
        private readonly PasswordVerification $passwordVerification,
    )
    {
    }

    public function beforeGetFormHtml(Main $subject): void
    {
        $this->passwordVerification->remove($subject);
    }
}
