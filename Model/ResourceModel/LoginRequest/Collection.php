<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Model\ResourceModel\LoginRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            \Opengento\Hoodoor\Model\LoginRequest::class,
            \Opengento\Hoodoor\Model\ResourceModel\LoginRequest::class
        );
    }
}
