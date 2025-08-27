<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * StartQuizAsset
 */
class StartQuizAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/start-quiz.js',
    ];
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}
