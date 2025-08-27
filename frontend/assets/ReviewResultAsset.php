<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * ReviewResultAsset
 */
class ReviewResultAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/review-results.js',
    ];
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}
