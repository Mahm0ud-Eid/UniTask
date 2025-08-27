<?php

namespace frontend\modules\admin;

use yii\base\Module;

/**
 * admin module definition class
 */
class Admin extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
