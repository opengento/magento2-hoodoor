<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service;

use Magento\Framework\Math\Random;

class Password
{
    public function __construct(
        private readonly Random $random
    ) {
    }

    public function generate(): string
    {
        return $this->random->getUniqueHash();
    }
}
