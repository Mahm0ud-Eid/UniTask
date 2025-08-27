<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var frontend\modules\admin\models\search\AdminsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title= "Admins";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-index">
    <p>
        <?= Html::a(Yii::t('app', 'Add {modelClass}', [
            'modelClass' => 'Admin',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'email',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
