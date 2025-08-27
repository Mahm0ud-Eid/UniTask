<?php

/**
 * Class DepartmentsEnum
 * @package common\enum
 */

namespace common\enum;

class DepartmentsEnum
{
    public const ICT = 1;
    public const Mechatronics = 2;
    public const Autotronics = 3;
    public const RenewableEnergy = 4;

    public const LABEL = [
        self::ICT => 'ICT',
        self::Mechatronics => 'Mechatronics',
        self::Autotronics => 'Autotronics',
        self::RenewableEnergy => 'Renewable Energy',
    ];
}
