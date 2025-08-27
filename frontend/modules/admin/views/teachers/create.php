<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'Add teachers');
$this->params['breadcrumbs'][] = ['label' => "Teachers", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add-teachers">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
