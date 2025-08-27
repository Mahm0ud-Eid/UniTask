<?php

use yii\helpers\Html;
use frontend\modules\admin\models\students\AddFormModel;

/* @var $this yii\web\View */
/* @var $model  AddFormModel */

$this->title = Yii::t('app', 'Add students');
$this->params['breadcrumbs'][] = ['label' => "Students", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add-students">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
