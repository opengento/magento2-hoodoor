<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Opengento\Hoodoor\Processor\EmailProcessor;

class Queue
{
    public function __construct(
        private readonly Request $requestService,
        private readonly EmailProcessor $emailProcessor
    ) {
    }

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
