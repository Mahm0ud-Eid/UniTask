<?php

/**
 * Class YearsEnum
 * @package common\enum
 */

namespace common\enum;

class YearsEnum
{
    public const FIRSTYEAR_1 = 1;
    public const FIRSTYEAR_2 = 2;
    public const SECONDYEAR_1 = 3;
    public const SECONDYEAR_2 = 4;
    public const THIRDYEAR_1 = 5;
    public const THIRDYEAR_2 = 6;
    public const FOURTHYEAR_1 = 7;
    public const FOURTHYEAR_2 = 8;

    public const LABEL = [
        self::FIRSTYEAR_1 => 'First Year - 1st Semester',
        self::FIRSTYEAR_2 => 'First Year - 2nd Semester',
        self::SECONDYEAR_1 => 'Second Year - 1st Semester',
        self::SECONDYEAR_2 => 'Second Year - 2nd Semester',
        self::THIRDYEAR_1 => 'Third Year - 1st Semester',
        self::THIRDYEAR_2 => 'Third Year - 2nd Semester',
        self::FOURTHYEAR_1 => 'Fourth Year - 1st Semester',
        self::FOURTHYEAR_2 => 'Fourth Year - 2nd Semester',
    ];
}
