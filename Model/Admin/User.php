<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Model\Admin;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\User\Model\Spi\NotificatorInterface;
use Magento\User\Model\UserValidationRules;

class User extends \Magento\User\Model\User
{
    protected $serializer;

    /**
     * @param \Opengento\PasswordLessLogin\Model\ResourceModel\Admin\User $resourceModel
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\User\Helper\Data $userData
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\User\Model\UserValidationRules $validationRules
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param \Magento\Framework\App\DeploymentConfig|null $deploymentConfig
     * @param \Magento\User\Model\Spi\NotificatorInterface|null $notificator
     */
    public function __construct(
        protected readonly \Opengento\PasswordLessLogin\Model\ResourceModel\Admin\User $resourceModel,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\User\Helper\Data $userData,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UserValidationRules $validationRules,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null,
        DeploymentConfig $deploymentConfig = null,
        ?NotificatorInterface $notificator = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $userData,
            $config,
            $validatorObjectFactory,
            $roleFactory,
            $transportBuilder,
            $encryptor,
            $storeManager,
            $validationRules,
            $resource,
            $resourceCollection,
            $data,
            $serializer,
            $deploymentConfig,
            $notificator
        );
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByEmail(string $email): static
    {
        $data = $this->resourceModel->loadByEmail($email);
        if ($data !== false) {
            $this->setData($data);
            $this->setOrigData();
        }
        return $this;
    }
}
