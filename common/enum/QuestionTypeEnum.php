<?php

/**
 * Class YearsEnum
 * @package common\enum
 */

namespace common\enum;

class QuestionTypeEnum
{
    public const MCQ = 1;
    public const TRUE_FALSE = 2;
    public const TEXT = 3;

    public const LABEL = [
        self::MCQ => 'MCQ',
        self::TRUE_FALSE => 'True/False',
        self::TEXT => 'Text',
    ];
}
