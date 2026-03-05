<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Account;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page;

class Edit extends \Magento\Customer\Controller\Account\Edit
{
    /** Redirect to account dashboard if a password change is requested, otherwise render normally. */
    #[\Override]
    public function execute(): ResponseInterface|Page
    {
        if ($this->getRequest()->getParam('changepass')) {
            return $this->_redirect('customer/account');
        }
        return parent::execute();
    }
}
