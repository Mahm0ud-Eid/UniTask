<?php

use common\models\Departments;
use common\models\Semesters;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Subjects $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="subjects-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'credits')->textInput() ?>

    <?= $form->field($model, 'department_id')->dropDownList(ArrayHelper::map(Departments::find()->all(), 'id', 'name'), ['prompt' => 'Select Department']) ?>

    <?= $form->field($model, 'semester_id')->dropDownList(ArrayHelper::map(Semesters::find()->all(), 'id', 'name'), ['prompt' => 'Select Semester']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
