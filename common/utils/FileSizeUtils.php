<?php

namespace common\utils;

class FileSizeUtils
{
    private const SIZE_FACTOR = 1024;
    private const UNIT_SUFFIXES = ['B', 'KB', 'MB', 'GB', 'TB'];

    public static function getSize($size)
    {
        $i = 0;
        $unitCount = count(self::UNIT_SUFFIXES) - 1;

        while ($size >= self::SIZE_FACTOR && $i < $unitCount) {
            $size /= self::SIZE_FACTOR;
            $i++;
        }

        return round($size, 2) . ' ' . self::UNIT_SUFFIXES[$i];
    }
}
