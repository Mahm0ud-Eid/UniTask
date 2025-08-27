<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Subjects $model */

$this->title = Yii::t('app', 'Create Subjects');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subjects-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
