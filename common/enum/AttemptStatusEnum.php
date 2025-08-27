<?php

/**
 * Class AttemptStatusEnum
 * @package common\enum
 */

namespace common\enum;

class AttemptStatusEnum
{
    public const NEW = 1;
    public const IN_PROGRESS = 2;
    public const FINISHED = 3;
    public const UNDER_REVIEW = 4;
    public const REVIEWED = 5;
    public const NEEDS_REVIEW = 6;
    public const NOT_STARTED = 7;

    public const LABEL = [
        self::NEW => 'New',
        self::IN_PROGRESS => 'In Progress',
        self::FINISHED => 'Finished',
        self::UNDER_REVIEW => 'Under Review',
        self::REVIEWED => 'Reviewed',
        self::NEEDS_REVIEW => 'Needs Review',
        self::NOT_STARTED => 'Not Started',
    ];

    public const BADGE = [
        self::NEW => 'badge badge-info',
        self::IN_PROGRESS => 'badge badge-primary',
        self::FINISHED => 'badge badge-warning',
        self::UNDER_REVIEW => 'badge badge-warning',
        self::REVIEWED => 'badge badge-success',
        self::NEEDS_REVIEW => 'badge badge-danger',
        self::NOT_STARTED => 'badge badge-secondary',
    ];
}
