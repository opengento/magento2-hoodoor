<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Account;

class Edit extends \Magento\Customer\Controller\Account\Edit
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('changepass')) {
            return $this->_redirect('customer/account');
        }
        return parent::execute();
    }
}
