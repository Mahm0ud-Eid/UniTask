<?php

namespace frontend\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Import students asset
 */
class ImportStuAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/import-students.js',
        'js/read-excel/read-excel-file.min.js'
    ];
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}
