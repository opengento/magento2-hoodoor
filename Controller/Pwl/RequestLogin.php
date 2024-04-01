<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Controller\Pwl;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Service\Queue;
use Opengento\PasswordLessLogin\Service\Request;

class RequestLogin implements HttpPostActionInterface
{
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Opengento\PasswordLessLogin\Service\Request $requestService
     * @param \Opengento\PasswordLessLogin\Service\Queue $queueService
     */
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly RedirectFactory $redirectFactory,
        protected readonly MessageManager $messageManager,
        protected readonly FormKey $formKey,
        protected readonly Request $requestService,
        protected readonly Queue $queueService
    ) {
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirect = $this->redirectFactory->create();

        $params = $this->request->getParams();
        if ($params) {
            $isFormKey = $this->formKey->isPresent();
            if (!$isFormKey) {
                $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));
                return $redirect->setPath('*/*/login');
            }
            if (!isset($params['login']['username'])) {
                $this->messageManager->addErrorMessage(__('You must enter a valid email address.'));
                return $redirect->setPath('*/*/login');
            } else {
                try {
                    $this->queueService->add($params, 'customer');
                    $this->messageManager->addSuccessMessage(__('If a customer account exists, you will receive an email to proceed with your request.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $redirect->setPath('*/*/login');
                }
            }
        }

        return $redirect->setPath('customer/account');
    }
}
