<?php

use common\models\Subjects;
use frontend\modules\admin\models\teachers\AddTeacherFormModel;
use kartik\form\ActiveForm;
use kartik\password\PasswordInput;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model AddTeacherFormModel */
/* @var $form yii\widgets\ActiveForm */
$subjects = Subjects::find()->select(['name', 'id'])->indexBy('id')->column();
?>

<div class="teachers-form">

    <?php Pjax::begin(['id' => 'teachers-form']) ?>

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
    <?= $form->field($model, 'subjects')->checkboxList($subjects) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
