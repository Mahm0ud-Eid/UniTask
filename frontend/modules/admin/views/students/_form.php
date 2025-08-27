<?php

use common\models\Departments;
use common\models\Semesters;
use frontend\modules\admin\models\students\AddFormModel;
use kartik\password\PasswordInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model AddFormModel */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<div class="students-form">

    <?php Pjax::begin(['id' => 'students-form']) ?>

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 200]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 200]) ?>
    <?= $form->field($model, 'semester_id')->dropDownList(ArrayHelper::map(
        Semesters::find()->all(),
        'id',
        'name'
    ), ['prompt'=>'Select Year'])->label('Year')?>
    <?= $form->field($model, 'department_id')->dropDownList(ArrayHelper::map(
        Departments::find()->all(),
        'id',
        'name'
    ), ['prompt'=>'Select Department'])->label('Department')?>
    <?= $form->field($model, 'password')->widget(PasswordInput::class, [
            'pluginOptions' => [
                'showMeter' => true,
                'size' => 'lg',
                'toggleMask' => true,
            ]
        ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
