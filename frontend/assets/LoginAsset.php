<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/login.css',
        'css/animate.min.css',
    ];
    public $js = [
        'js/login.js',
        'https://hcaptcha.com/1/api.js',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
