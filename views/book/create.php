<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Book */
/* @var $authors \yii\db\ActiveRecord[] */
/* @var $authorsList array */

$this->title = 'Создать книгу';
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
        'authorsList' => $authorsList,
    ]) ?>

</div>
