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
    /**
     * @param \Magento\Framework\Math\Random $random
     */
    public function __construct(
        protected readonly Random $random
    ) {
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate(): string
    {
        return $this->random->getUniqueHash();
    }
}
