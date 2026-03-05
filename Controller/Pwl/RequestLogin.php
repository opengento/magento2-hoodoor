<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Pwl;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\Manager as MessageManager;
use Opengento\Hoodoor\Service\Queue;

class RequestLogin implements HttpPostActionInterface
{
    use RequestLoginTrait;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly MessageManager $messageManager,
        private readonly FormKey $formKey,
        private readonly Queue $queueService
    ) {
    }

    protected function isFormKeyValid(): bool
    {
        return $this->formKey->isPresent();
    }

    protected function getLoginType(): string
    {
        return 'customer';
    }

    protected function getErrorRedirectPath(): string
    {
        return '*/*/login';
    }

    protected function getSuccessRedirectPath(): string
    {
        return 'customer/account';
    }

    protected function getSuccessMessage(): string
    {
        return 'If a customer account exists, you will receive an email to proceed with your request.';
    }
}
