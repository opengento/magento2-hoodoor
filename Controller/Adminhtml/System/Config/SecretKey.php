<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Math\Random;

class SecretKey extends Action
{
    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        protected readonly JsonFactory $resultJsonFactory,
        protected readonly Random $random,
        Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $randomString = $this->random->getRandomString(16);
        return $result->setData(['secret_key' => $randomString]);
    }
}
