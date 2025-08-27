<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use frontend\assets\ErrorAsset;
use yii\helpers\Html;

$this->title = $name;
$this->params['breadcrumbs'] = [['label' => $this->title]];
ErrorAsset::register($this);
?>
<div id="notfound">
    <div class="notfound">
        <div class="notfound-404">
            <h1><?= Yii::$app->response->statusCode ?></h1>
        </div>
        <h2><?= nl2br(Html::encode($message)) ?></h2>
        <p>Please either report this error to an administrator or return back and forget you were here...</p>
        <a href="/">Back To Homepage</a>
    </div>
</div>