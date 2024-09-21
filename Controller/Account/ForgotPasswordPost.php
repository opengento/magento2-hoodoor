<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Account;

use Magento\Framework\App\ResponseInterface;

class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
    public function execute(): ResponseInterface
    {
        $this->messageManager->addErrorMessage(__('Access denied.'));
        return $this->_redirect('customer/account');
    }
}
