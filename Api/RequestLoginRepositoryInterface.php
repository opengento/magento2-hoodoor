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
     * Retrieve a login request by email address.
     *
     * @param string $email
     * @return LoginRequest
     */
    public function get(string $email): LoginRequest;

    /**
     * Retrieve a login request by its entity ID.
     *
     * @param string $id
     * @return LoginRequest
     */
    public function getById(string $id): LoginRequest;

    /**
     * Persist a login request to the database.
     *
     * @param LoginRequest $model
     * @return bool
     */
    public function save(LoginRequest $model): bool;

    /**
     * Delete a login request from the database.
     *
     * @param LoginRequest $model
     * @return bool
     */
    public function delete(LoginRequest $model): bool;

    /**
     * Mark a login request as used so it cannot be reused.
     *
     * @param LoginRequest $model
     * @return bool
     */
    public function lock(LoginRequest $model): bool;
}
