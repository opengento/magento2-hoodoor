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
    public function get(string $email): LoginRequest;

    public function getById(string $id): LoginRequest;

    public function save(LoginRequest $model): bool;

    public function delete(LoginRequest $model): bool;

    public function lock(LoginRequest $model): bool;
}
