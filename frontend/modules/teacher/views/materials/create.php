<?php

use common\enum\FileExtensionsEnum;
use common\models\Subjects;
use frontend\modules\teacher\models\files\UploadFileModel;
use kartik\file\FileInput;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UploadFileModel */
/* @var $modelMaterial common\models\Materials */
/* @var $subject Subjects */

$this->title = $subject->name;
$this->params['breadcrumbs'][] = $this->title;
$file_types = "pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar";
$extensions = explode(',', $file_types);
?>
<div class="task-view">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#task" data-bs-toggle="tab">Lecture</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="task" role="tabpanel" aria-labelledby="task-tab">
                    <div class="row">
                        <?php $form = ActiveForm::begin([
                            'id' => 'upload-file-form',
                            'options' => ['enctype' => 'multipart/form-data'],
                        ]); ?>
                        <div class="col-md-12">
                            <?= $form->field($modelMaterial, 'title')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($modelMaterial, 'description')->textarea() ?>
                        </div>
                        <div class="col-md-12">
                           <?= $form->field($model, 'files[]')->widget(
                               FileInput::class,
                               [
                                     'options'=>[
                                        'multiple'=>true,
                                     ],
                                     'pluginOptions' => [
                                        'allowedFileExtensions' => $extensions,
                                         'previewFileType' => 'any',
                                         'maxFileCount' => 20,
                                     ]
                                     ]
                           ) ?>
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
