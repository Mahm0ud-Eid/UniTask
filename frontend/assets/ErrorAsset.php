<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * asset bundle for error pages
 */
class ErrorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/error.css',
    ];
}
