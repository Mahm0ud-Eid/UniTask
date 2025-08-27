<?php

/**
 * Class YearsEnum
 * @package common\enum
 */

namespace common\enum;

class QuizVisibilityEnum
{
    public const AFTER_QUIZ = 1;
    public const AFTER_QUIZ_TIME = 2;
    public const NEVER = 3;

    public const LABEL = [
        self::AFTER_QUIZ => 'After quiz',
        self::AFTER_QUIZ_TIME => 'After quiz time',
        self::NEVER => 'Never',
    ];
}
