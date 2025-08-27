<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CustomAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/adminlte.min.css',
        'css/style.css',
    ];
    public $js = [
        'js/adminlte.min.js',
    ];
}
