<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Model\ResourceModel\Admin;

class User extends \Magento\User\Model\ResourceModel\User
{
    /** Load an admin user row from the database by email address. */
    public function loadByEmail(string $email): bool|array
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable())->where('email=:email');

        $binds = ['email' => $email];

        return $connection->fetchRow($select, $binds);
    }
}
