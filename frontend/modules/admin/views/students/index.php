<?php

use frontend\assets\ImportStuAsset;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\models\Departments;
use common\models\Semesters;

/* @var yii\web\View $this */
/* @var frontend\modules\admin\models\search\StudentsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title= "Students";
$this->params['breadcrumbs'][] = $this->title;
ImportStuAsset::register($this);
?>
<div class="students-index">
    <p>
        <?= Html::a(Yii::t('app', 'Add {modelClass}', [
            'modelClass' => 'Students',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <button class="btn btn-primary" id="import-students">Import Students</button>
    <div class="input-group mb-3 d-none" id="import-students-form">
        <input type="file" class="form-control" id="import-students-file">
        <button class="btn btn-outline-secondary" type="button" id="import-students-submit">Import</button>
    </div>
    <div id="options-import">
    </div>


    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'email',
            [
                'attribute' => 'department',
                'format' => 'raw',
                'value' => 'studentDepartmentYears.department.name',
                'filter' => ArrayHelper::map(Departments::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'semester',
                'format' => 'raw',
                'value' => 'studentDepartmentYears.semester.name',
                'filter' => ArrayHelper::map(Semesters::find()->all(), 'id', 'name'),
            ],
            ['class' => 'yii\grid\ActionColumn'],

        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
