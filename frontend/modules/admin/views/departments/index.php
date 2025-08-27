<?php

use common\models\Departments;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departments-index">

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \kartik\grid\SerialColumn::class,
            'header' => 'No.',
                ],
            'id',
            'name',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Departments $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
        'responsive'=>true,
        'responsiveWrap'=>false,
        'hover'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fas fa-building"></i> Departments</h3>',
            'type'=>'success',
            'before'=>Html::a('<i class="fas fa-plus"></i> Create Department', ['create'], ['class' => 'btn btn-success']),
            'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            'footer'=>false
        ],
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Departments-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Departments -'.date('d-M-Y')],
            GridView::PDF => ['label' => 'Export as PDF', 'filename' => 'Departments -'.date('d-M-Y'),
                'config' => [
                    'methods' => [
                        'SetHeader' => ['UniTask|Departments|Generated On: ' . date("r")],
                        'SetFooter' => ['|Page {PAGENO}|'],
                    ],
                    'options' => [
                        'title' => 'Departments',
                    ],
                ],
            ],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Departments -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Departments -'.date('d-M-Y')],
            GridView::JSON=> ['label' => 'Export as JSON', 'filename' => 'Departments -'.date('d-M-Y')],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
