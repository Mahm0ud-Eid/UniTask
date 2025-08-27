<?php


use common\models\TaskAttempt;
use common\models\Tasks;
use common\models\User;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\grid\EditableColumn;
use yii\helpers\HtmlPurifier;

/* @var User $model */
/* @var Tasks $task */
/* @var TaskAttempt[] $results */

$this->title = 'Results';
$dataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'pagination' => false,
    'sort' => [
        'attributes' => [
            'user.name',
            'grade',
            'student_comment',
            'teacher_comment',
        ],
    ],
]);

?>
<div class="row">
    <div class="col-md-12">
        <h2><?= $task->title ?></h2>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table m-t-30 table-striped breakpoint-lg'],
            'columns' => [
                [
                    'attribute' => 'user.name',
                    'label' => 'Student Name',
                    'enableSorting' => true,
                ],
                [
                    'attribute' => 'grade',
                    'label' => 'Grade',
                    'enableSorting' => true,
                    'class' => EditableColumn::class,
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'name' => 'grade',
                            'displayValue' => $model->grade,
                            'inputType' => Editable::INPUT_TEXT,
                            'additionalData' => [
                                'id' => $model->id,
                            ],
                            'asPopover' => false,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ];
                    },
                ],
                [
                    'attribute' => 'student_comment',
                    'label' => 'Student Comment',
                    'hiddenFromExport' => true,
                    'contentOptions' => ['class' => 'long-td'],
                    'value' => function ($model) {
                        return HtmlPurifier::process($model->student_comment);
                    },
                ],

                [
                    'attribute' => 'teacher_comment',
                    'class' => EditableColumn::class,
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'name' => 'teacher_comment',
                            'displayValue' => $model->teacher_comment,
                            'inputType' => Editable::INPUT_TEXTAREA,
                            'additionalData' => [
                                'id' => $model->id,
                            ],
                            'asPopover' => false,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ];
                    },
                    'hiddenFromExport' => true,
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{download} {delete}',
                    'buttons' => [
                        'download' => function ($url, $model, $key) {
                            if (!empty($model->files)) {
                                return Html::a('Download', ['/teacher/tasks/' . $model->task->subject->id . '/download-files/' . $model->id], [
                                    'class' => 'btn btn-primary',
                                ]);
                            }
                            return 'No files uploaded';
                        },
                        'delete' => function ($url, $model, $key) {
                            if (!empty($model->files)) {
                                return Html::a('Delete', ['/teacher/tasks/' . $model->task->subject->id . '/delete-files/' . $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to delete this result?'),
                                    ],
                                ]);
                            }
                            return '';
                        },
                    ],
                    'contentOptions' => ['class' => 'text-center'],
                ],
            ],
            'responsive'=>true,
            'responsiveWrap'=>true,
            'hover'=>true,
            'panel' => [
                'before' => Html::a('Download All', ['/teacher/tasks/' . $task->subject_id . '/download-all-files/' . $task->id], [
                    'class' => 'btn btn-primary',
                ]),
                'type'=>'success',
                'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['/teacher/tasks/'.$task->subject_id .'/results/' . $task->id], ['class' => 'btn btn-info']),
                'footer'=>false
            ],
            'exportConfig' => [
                GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title],
                GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title],
                GridView::PDF => ['label' => 'Export as PDF', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title,
                    'config' => [
                        'methods' => [
                            'SetHeader' => ['UniTask|Results - '. $task->title .'|Generated On: ' . date("r")],
                            'SetFooter' => ['|Page {PAGENO}|'],
                        ],
                        'options' => [
                            'title' => 'Results - '. $task->title,
                        ],
                    ],
                ],
                GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title],
                GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title],
                GridView::JSON=> ['label' => 'Export as JSON', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $task->title],
            ],
        ]) ?>
        </div>
    </div>
</div>
