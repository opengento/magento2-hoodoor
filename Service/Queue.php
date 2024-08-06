<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Magento\Framework\Message\Manager;
use Opengento\Hoodoor\Processor\EmailProcessor;

class Queue
{
    /**
     * @param \Opengento\Hoodoor\Service\Request $requestService
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Opengento\Hoodoor\Processor\EmailProcessor $emailProcessor
     */
    public function __construct( //phpcs:ignore
        protected readonly Request $requestService,
        protected readonly Manager $messageManager,
        protected readonly EmailProcessor $emailProcessor
    ) {
    }

    /**
     * Add Request To Queue
     *
     * @param array $params
     * @param string $type
     * @return true
     */
    public function add(array $params, string $type): bool
    {
        $success = $this->requestService->create($params['login']['username'], $type);
        if ($success) {
            $this->emailProcessor->sendMail($params['login']['username'], $type);
            return true;
        }
        return false;
    }
}
