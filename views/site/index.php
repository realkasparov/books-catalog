<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProviderBooks yii\data\ActiveDataProvider */
/* @var $dataProviderAuthors yii\data\ActiveDataProvider */

?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProviderBooks,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'author',
        ],
    ]); ?>
</div>

<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProviderAuthors,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'bookCount',
        ],
    ]); ?>
</div>