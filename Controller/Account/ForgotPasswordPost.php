<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Account;

class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
    /**
     * Deny access to this forgot password feature
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->messageManager->addErrorMessage(__('Access denied.'));
        return $this->_redirect('customer/account');
    }
}
