<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapPluginAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/global/sweetalert2.all.min.js',
        'js/theme.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class
    ];
}
