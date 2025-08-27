<?php

use frontend\modules\teacher\models\tasks\AddTaskModel;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var AddTaskModel $model */

$this->title = Yii::t('app', 'Create Tasks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
