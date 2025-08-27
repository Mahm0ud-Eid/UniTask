<?php

namespace common\enum;

/**
 * Class ConfirmationTokenType
 * @package common\enum
 */
class ConfirmationTokenType
{
    public const LABEL = [
        self::PASSWORD_RESET_CONFIRMATION => 'Password Reset Confirmation',
    ];
    public const PASSWORD_RESET_CONFIRMATION = 1;
}
