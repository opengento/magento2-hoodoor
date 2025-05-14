<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Plugin\AdobeImsReAuth\Block\System\Account\Edit;

use Magento\Backend\Block\System\Account\Edit\Form;
use Opengento\Hoodoor\Service\Admin\PasswordVerification;

class RemoveReAuthVerification
{
    public function __construct(
        private readonly PasswordVerification $passwordVerification,
    )
    {
    }

    public function beforeGetFormHtml(Form $subject): void
    {
        $this->passwordVerification->remove($subject);
    }
}
