<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Model\ResourceModel\LoginRequest as LoginRequestResource;

class LoginRequestRepository implements RequestLoginRepositoryInterface
{
    public function __construct(
        private readonly LoginRequestFactory $loginRequestFactory,
        private readonly LoginRequestResource $loginRequestResource
    ) {
    }

    public function get(string $email): LoginRequest
    {
        $model = $this->loginRequestFactory->create();
        $this->loginRequestResource->load($model, $email, 'email');
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Request with email "%1" does not exist.', $email));
        }
        return $model;
    }

    public function getById(string $id): LoginRequest
    {
        $model = $this->loginRequestFactory->create();
        $this->loginRequestResource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Request with id "%1" does not exist.', $id));
        }
        return $model;
    }

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
