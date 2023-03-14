<?php

use common\models\UserJsonData;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\UserJsonDataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Json Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-json-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'type',
            'json:ntext',
            'created_at',
            'updated_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{view}{delete}',
                'urlCreator' => function ($action, UserJsonData $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>