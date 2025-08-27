<?php

use common\models\Subjects;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Subjects');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="subjects-index">

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \kartik\grid\SerialColumn::class,
                'header' => 'No.',
            ],
            'id',
            'name',
            'description:ntext',
            'credits',
            [
                'attribute' => 'department.name',
                'label' => 'Department',
            ],
            [
                'attribute' => 'semester.name',
                'label' => 'Semester',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Subjects $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
        'responsive'=>true,
        'responsiveWrap'=>false,
        'hover'=>true,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fas fa-book"></i> Subjects</h3>',
            'type'=>'success',
            'before'=>Html::a('<i class="fas fa-plus"></i> Create Subject', ['create'], ['class' => 'btn btn-success']),
            'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            'footer'=>false
        ],
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Subjects-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Subjects -'.date('d-M-Y')],
            GridView::PDF => ['label' => 'Export as PDF', 'filename' => 'Subjects -'.date('d-M-Y'),
                'config' => [
                    'methods' => [
                        'SetHeader' => ['UniTask|Subjects|Generated On: ' . date("r")],
                        'SetFooter' => ['|Page {PAGENO}|'],
                    ],
                    'options' => [
                        'title' => 'Departments',
                    ],
                ],
            ],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Subjects -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Subjects -'.date('d-M-Y')],
            GridView::JSON=> ['label' => 'Export as JSON', 'filename' => 'Subjects -'.date('d-M-Y')],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
