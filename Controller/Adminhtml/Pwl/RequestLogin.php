<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Controller\Adminhtml\Pwl;

use Magento\Backend\Block\Admin\Formkey;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\PasswordLessLogin\Service\Queue;

class RequestLogin implements HttpPostActionInterface
{
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Magento\Backend\Block\Admin\Formkey $formKey
     * @param \Opengento\PasswordLessLogin\Service\Queue $queueService
     */
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly RedirectFactory $redirectFactory,
        protected readonly MessageManager $messageManager,
        protected readonly FormKey $formKey,
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
            $isFormKey = $this->formKey->isEmpty();
            if ($isFormKey) {
                $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));
                return $redirect->setPath('*/*');
            }
            if (!isset($params['login']['username'])) {
                $this->messageManager->addErrorMessage(__('You must enter a valid email address.'));
                return $redirect->setPath('*/*');
            } else {
                try {
                    $this->queueService->add($params, 'admin');
                    $this->messageManager->addSuccessMessage(__('If an account exists, you will receive an email to proceed with your request.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $redirect->setPath('*/*');
                }
            }
        }

        return $redirect->setPath('*/*');
    }
}
