<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\PasswordLessLogin\Api\RequestLoginRepositoryInterface;
use Opengento\PasswordLessLogin\Model\ResourceModel\LoginRequest as LoginRequestResource;

class LoginRequestRepository implements RequestLoginRepositoryInterface
{
    public function __construct(
        protected readonly LoginRequestFactory $loginRequestFactory,
        protected readonly LoginRequestResource $loginRequestResource
    ) {
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(string $email): LoginRequest
    {
        $model = $this->loginRequestFactory->create();
        $this->loginRequestResource->load($model, $email, 'email');
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('RequestException with email "%1" does not exist.', $email));
        }
        return $model;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(string $id): LoginRequest
    {
        $model = $this->loginRequestFactory->create();
        $this->loginRequestResource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('RequestException with id "%1" does not exist.', $id));
        }
        return $model;
    }

    /**
     * @throws \Exception
     */
    public function save(LoginRequest $model): bool
    {
        try {
            $this->loginRequestResource->save($model);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the request: %1',
                $e->getMessage()
            ));
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function delete(LoginRequest $model): bool
    {
        try {
            $this->loginRequestResource->delete($model);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__(
                'Could not delete the request: %1',
                $e->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param \Opengento\PasswordLessLogin\Model\LoginRequest $model
     * @return bool
     */
    public function lock(LoginRequest $model): bool
    {
        try {
            $model->setIsUsed(1);
            $this->save($model);
        } catch (\Exception $e) {
            throw new (__(
                'Could not lock the request: %1',
                $e->getMessage()
            ));
        }
        return true;
    }
}
