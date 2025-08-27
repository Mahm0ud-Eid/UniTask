<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * CreateQuizAsset
 */
class CreateQuizAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/create-quiz.js',
    ];
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}
