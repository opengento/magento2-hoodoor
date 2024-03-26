<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LoginRequest extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('customer_login_request_queue', 'entity_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param $value
     * @param $field
     * @return $this|\Opengento\PasswordLessLogin\Model\ResourceModel\LoginRequest
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null): LoginRequest|static
    {
        if($field === 'email') {
            $object->beforeLoad($value, $field);
            $connection = $this->getConnection();
            if ($connection && $value !== null) {
                $select = $connection->select()
                    ->from($this->getMainTable())
                    ->where('email = (?)', $value)
                    ->order('entity_id DESC');
                $data = $connection->fetchRow($select);
                if ($data) {
                    $object->setData($data);
                }
            }

            $this->unserializeFields($object);
            $this->_afterLoad($object);
            $object->afterLoad();
            $object->setOrigData();
            $object->setHasDataChanges(false);

            return $this;
        }

        return parent::load($object, $value, $field);
    }
}
