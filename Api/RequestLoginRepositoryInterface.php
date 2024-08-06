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
     * Get Login Request
     *
     * @param string $email
     * @return LoginRequest
     */
    public function get(string $email): LoginRequest;

    /**
     * Get Login Request By Id
     *
     * @param string $id
     * @return LoginRequest
     */
    public function getById(string $id): LoginRequest;

    /**
     * Save Login Request
     *
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function save(LoginRequest $model): bool;

    /**
     * Delete Login Request
     *
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function delete(LoginRequest $model): bool;

    /**
     * Lock Login Request
     *
     * @param \Opengento\Hoodoor\Model\LoginRequest $model
     * @return bool
     */
    public function lock(LoginRequest $model): bool;
}
