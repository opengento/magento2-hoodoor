<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Model;

class Email
{
    public const XML_PATH_PASSWORDLESSLOGIN_TEMPLATE_ID = 'passwordlesslogin/email/template_id';
    public const XML_PATH_PASSWORDLESSLOGIN_SENDER_EMAIL = 'passwordlesslogin/email/sender_email';
    public const XML_PATH_PASSWORDLESSLOGIN_SENDER_NAME = 'passwordlesslogin/email/sender_name';

    public const XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY = 'passwordlesslogin/security/secret_key';
}
