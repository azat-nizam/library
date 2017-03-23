<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<div class="alert alert-warning">
	<strong>Внимание!</strong> Для удаления категории сотрите её название.
</div>
<?php
Pjax::begin(['id' => 'add-category-grid']);
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => $columns,
	'tableOptions' => [
		'class' => 'table table-bordered table-own-striped'
	],
	'rowOptions' => function ($model, $key, $index, $grid) {
		/*
		$class=$model['status'] == Book::BOOK_INACCESSIBLE ? ' inaccessible-book':'';
		return [
			'key'=>$key,
			'index'=>$index,
			'class'=>$class,
		];
		*/
	},
]);
Pjax::end();
?>
<button type='button' class='add-category btn btn-primary'>Добавить категорию</button>

