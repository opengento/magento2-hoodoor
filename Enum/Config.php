<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Enum;

enum Config: string
{
    case XML_PATH_PASSWORDLESSLOGIN_ENABLE_ADMIN = 'passwordlesslogin/general/enable_admin';
    case XML_PATH_PASSWORDLESSLOGIN_ENABLE_FRONTEND = 'passwordlesslogin/general/enable_frontend';
    case XML_PATH_PASSWORDLESSLOGIN_TEMPLATE_ID = 'passwordlesslogin/email/template_id';
    case XML_PATH_PASSWORDLESSLOGIN_SENDER_EMAIL = 'passwordlesslogin/email/sender_email';
    case XML_PATH_PASSWORDLESSLOGIN_SENDER_NAME = 'passwordlesslogin/email/sender_name';
    case XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY = 'passwordlesslogin/security/secret_key';
    case XML_PATH_PASSWORDLESSLOGIN_MAX_TIME_EXPIRATION = 'passwordlesslogin/security/max_time_expiration';
}
