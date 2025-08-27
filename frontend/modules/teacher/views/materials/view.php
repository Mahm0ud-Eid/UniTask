<?php

use common\enum\FileExtensionsEnum;
use common\models\Subjects;
use common\utils\FileSizeUtils;
use common\utils\TimeUtils;
use frontend\modules\teacher\models\files\UploadFileModel;
use kartik\file\FileInput;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model UploadFileModel */
/* @var $subject Subjects */
/* @var $material common\models\Materials */

$this->title = $material->title;
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
                <li class="nav-item">
                    <a class="nav-link" href="#files" data-bs-toggle="tab">Files</a>
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
                            'enableClientValidation' => false,
                        ]); ?>
                        <div class="col-md-12">
                            <?= $form->field($material, 'title')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($material, 'description')->textarea() ?>
                        </div>
                        <div class="col-md-12">
                           <?= $form->field($model, 'files[]')->widget(
                               FileInput::class,
                               [
                                     'options'=>[
                                        'multiple'=>true,
                                         'required' => false,
                                     ],
                                     'pluginOptions' => [
                                        'allowedFileExtensions' => $extensions,
                                        'previewFileType' => 'any',
                                     ]
                                     ]
                           ) ?>
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="tab-pane" id="files" role="tabpanel" aria-labelledby="files-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">File name</th>
                                    <th scope="col">File size</th>
                                    <th scope="col">From</th>
                                    <th scope="col">Uploaded By</th>
                                    <th scope="col">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($material->files as $file): ?>
                                    <tr>
                                        <th scope="row"><?= $file->id ?></th>
                                        <td><?= $file->file->name ?></td>
                                        <td><?= FileSizeUtils::getSize($file->file->size)?></td>
                                        <td><?= TimeUtils::getTimeAgo($file->file->created_at) ?></td>
                                        <td><?= $file->file->user->name ?></td>
                                        <td>
                                            <a href="<?= Url::to(['materials/download-file', 'id' => $material->subject_id, 'fileId' => $file->id]) ?>" class="btn btn-primary">Download</a>
                                            <a href="<?= Url::to(['materials/delete-file', 'id' => $material->subject_id, 'fileId' => $file->id]); ?>" class="btn btn-danger">Remove</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
