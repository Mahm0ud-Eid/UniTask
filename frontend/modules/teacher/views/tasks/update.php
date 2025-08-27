<?php

use frontend\modules\teacher\models\tasks\UpdateTaskModel;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var UpdateTaskModel $model */

$this->title = Yii::t('app', 'Update Tasks: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tasks-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
