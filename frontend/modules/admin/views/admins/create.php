<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'Add Admin');
$this->params['breadcrumbs'][] = ['label' => "Admins", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add-admin">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
