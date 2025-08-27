<?php

use yii\helpers\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model User */

$this->title = Yii::t('app', 'Update user: {id}', [
    'id' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="credit-bonus-code-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
