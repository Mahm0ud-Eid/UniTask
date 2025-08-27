<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Pjax;
use kartik\password\PasswordInput;

/* @var $this yii\web\View */
/* @var $model frontend\modules\admin\models\admins\AdminFormModel */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<div class="admins-form">

    <?php Pjax::begin(['id' => 'admins-form']) ?>

    <?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 200]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 200]) ?>
    <?= $form->field($model, 'password')->widget(PasswordInput::class, [
        'pluginOptions' => [
            'showMeter' => true,
            'size' => 'lg',
            'toggleMask' => true,
        ]
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
