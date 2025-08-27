<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Semesters $model */

$this->title = Yii::t('app', 'Create Semesters');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Semesters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semesters-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
