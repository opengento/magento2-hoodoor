<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Enum;

enum Config: string
{
    case XML_PATH_HOODOOR_ENABLE_ADMIN = 'hoodoor/general/enable_admin';
    case XML_PATH_HOODOOR_ENABLE_FRONTEND = 'hoodoor/general/enable_frontend';
    case XML_PATH_HOODOOR_TEMPLATE_ID = 'hoodoor/email/template_id';
    case XML_PATH_HOODOOR_SENDER_EMAIL = 'hoodoor/email/sender_email';
    case XML_PATH_HOODOOR_SENDER_NAME = 'hoodoor/email/sender_name';
    case XML_PATH_HOODOOR_SECRET_KEY = 'hoodoor/security/secret_key';
    case XML_PATH_HOODOOR_MAX_TIME_EXPIRATION = 'hoodoor/security/max_time_expiration';
}
