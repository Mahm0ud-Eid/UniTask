<?php


use common\enum\AttemptStatusEnum;
use common\models\QuizAttempt;
use common\models\Quizzes;
use common\models\User;
use kartik\grid\ActionColumn;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var User $model */
/* @var Quizzes $quiz */
/* @var QuizAttempt[] $results */

$this->title = 'Results';
$dataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'pagination' => false,
    'sort' => [
        'attributes' => [
            'user.name',
            'status',
            'grade',
        ],
    ],
]);

?>
<div class="row">
    <div class="col-md-12">
        <h2><?= $quiz->name ?></h2>
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
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::tag('span', AttemptStatusEnum::LABEL[$model->status], ['class' => AttemptStatusEnum::BADGE[$model->status]]);
                    },
                    'label' => 'Status',
                    'enableSorting' => true,
                    'headerOptions' => ['style' => 'width: 120px'],
                    'hiddenFromExport' => true,
                ],
                [
                    'attribute' => 'grade',
                    'label' => 'Grade',
                    'enableSorting' => true,
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{details} {delete}',
                    'buttons' => [
                        'details' => function ($url, $model, $key) {
                            if ($model->status !== AttemptStatusEnum::NOT_STARTED) {
                                return Html::a('<i class="fa fa-chevron-down"></i>', ['quizzes/' . $model->id . '/student-result'], ['class' => 'btn btn-primary']);
                            }
                            return '';
                        },
                        'delete' => function ($url, $model, $key) {
                            if ($model->status !== AttemptStatusEnum::NOT_STARTED) {
                                return Html::a('Delete', ['quizzes/' . $model->id . '/delete-result'], [
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
            'responsiveWrap'=>false,
            'hover'=>true,
            'panel' => [
                'type'=>'success',
                'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['/teacher/quizzes/'.$quiz->id .'/results'], ['class' => 'btn btn-info']),
                'footer'=>false
            ],
            'exportConfig' => [
                GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name],
                GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name],
                GridView::PDF => ['label' => 'Export as PDF', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name,
                    'config' => [
                        'methods' => [
                            'SetHeader' => ['UniTask|Results - '. $quiz->name .'|Generated On: ' . date("r")],
                            'SetFooter' => ['|Page {PAGENO}|'],
                        ],
                        'options' => [
                            'title' => 'Results - '. $quiz->name,
                        ],
                    ],
                ],
                GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name],
                GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name],
                GridView::JSON=> ['label' => 'Export as JSON', 'filename' => 'Results -'.date('d-M-Y') . ' - '. $quiz->name],
            ],
        ]) ?>
    </div>
</div>
