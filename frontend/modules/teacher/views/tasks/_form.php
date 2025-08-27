<?php

use common\enum\FileExtensionsEnum;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Tasks $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="tasks-form">
    <?php $form = ActiveForm::begin([
            'id' => 'tasks-form',
            'layout' => 'horizontal',
            'enableClientValidation' => true,
            'validateOnBlur' => true,
            'validateOnType' => true,
            'validateOnChange' => true,
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'wrapper' => 'col-sm-8',
                ],
            ],
        ]);
?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'active')->checkbox() ?>

    <?= $form->field($model, 'starts_at')->widget(
        DateTimePicker::class,
        [
                'name' => 'starts_at',
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'value' => date('Y-m-d H:i:s'),
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss',
                    'todayHighlight' => true,
                    'todayBtn' => true,
                ],
            ]
    ) ?>

    <?= $form->field($model, 'ends_at')->widget(
        DateTimePicker::class,
        [
            'name' => 'ends_at',
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'value' => date('Y-m-d H:i:s'),
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayHighlight' => true,
                'todayBtn' => true,
            ],
        ]
    ) ?>
    <?= $form->field($model, 'file_types')->checkboxList(
        FileExtensionsEnum::LABEL,
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                $extension = $value;
                $icon = FileExtensionsEnum::ICON[$value];
                $label = $label . " (." . $extension . ")";
                $input = Html::checkbox($name, $checked, ['value' => $value, 'label' => false]);
                return "<div class=\"checkbox-inline\"><label>{$input} <img src=\"{$icon}\"> {$label}</label></div>";
            }
        ]
    ) ?>
    <?= $form->field($model, 'results_visibility')->dropDownList(
        ['0' => 'Hidden', '1' => 'Visible'],
        ['prompt' => 'Select visibility']
    ) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
