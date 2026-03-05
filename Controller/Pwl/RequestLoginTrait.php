<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Controller\Pwl;

use Magento\Framework\Controller\Result\Redirect;

trait RequestLoginTrait
{
    /** Handle password-less login request form submission. */
    #[\Override]
    public function execute(): Redirect
    {
        $redirect = $this->redirectFactory->create();

        $params = $this->request->getParams();
        if ($params) {
            if (!$this->isFormKeyValid()) {
                $this->messageManager->addErrorMessage(
                    __('Invalid Form Key. Please refresh the page.')
                );
                return $redirect->setPath($this->getErrorRedirectPath());
            }
            if (!isset($params['login']['username'])) {
                $this->messageManager->addErrorMessage(
                    __('You must enter a valid email address.')
                );
                return $redirect->setPath($this->getErrorRedirectPath());
            } else {
                try {
                    $this->queueService->add($params, $this->getLoginType());
                    $this->messageManager->addSuccessMessage(__($this->getSuccessMessage()));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $redirect->setPath($this->getErrorRedirectPath());
                }
            }
        }

        return $redirect->setPath($this->getSuccessRedirectPath());
    }

    abstract protected function isFormKeyValid(): bool;

    abstract protected function getLoginType(): string;

    abstract protected function getErrorRedirectPath(): string;

    abstract protected function getSuccessRedirectPath(): string;

    abstract protected function getSuccessMessage(): string;
}
