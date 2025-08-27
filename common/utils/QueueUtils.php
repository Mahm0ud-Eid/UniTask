<?php

namespace common\utils;

use Exception;
use Yii;
use yii\db\Query;

class QueueUtils
{
    /**
     * @return mixed
     * @throws Exception
     */
    public static function getQueueLine()
    {
        return self::getSmallerNormalQueue();
    }

    public static function getSmallerNormalQueue()
    {
        $smallerQueue = self::getNormalQueues()[0];
        $smallerQueueValue = (new Query())->select('id')->from('queue')->count('id');
        if ((new Query())->select('id')->from('queue2')->count('id') < $smallerQueueValue) {
            $smallerQueue = self::getNormalQueues()[1];
        }
        if ((new Query())->select('id')->from('queue3')->count('id') < $smallerQueueValue) {
            $smallerQueue = self::getNormalQueues()[2];
        }
        if ((new Query())->select('id')->from('queue4')->count('id') < $smallerQueueValue) {
            $smallerQueue = self::getNormalQueues()[3];
        }
        if ((new Query())->select('id')->from('queue5')->count('id') < $smallerQueueValue) {
            $smallerQueue = self::getNormalQueues()[4];
        }
        return $smallerQueue;
    }
    private static function getNormalQueues()
    {
        return [
            Yii::$app->queue,
            Yii::$app->queue2,
            Yii::$app->queue3,
            Yii::$app->queue4,
            Yii::$app->queue5,
        ];
    }
}
