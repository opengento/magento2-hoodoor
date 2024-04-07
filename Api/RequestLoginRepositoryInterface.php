<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Api;

use Opengento\Hoodoor\Model\LoginRequest;

interface RequestLoginRepositoryInterface
{
    /**
     * @param string $email
     * @return LoginRequest
     */
    public function get(string $email): LoginRequest;

    /**
     * @param string $id
     * @return LoginRequest
     */
    public function getById(string $id): LoginRequest;

    /**
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function save(LoginRequest $model): bool;

    /**
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function delete(LoginRequest $model): bool;

    /**
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function lock(LoginRequest $model): bool;
}
