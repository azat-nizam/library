<?php
use yii\grid\GridView;
use frontend\models\Book;

// таблица для вывода списков книг
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => $columns,
	'tableOptions' => [
		'class' => 'table table-bordered table-own-striped'
	],
	'rowOptions' => function ($model, $key, $index, $grid) {
		$class=$model['status'] == Book::BOOK_INACCESSIBLE ? ' inaccessible-book':'';
		return [
			'key'=>$key,
			'index'=>$index,
			'class'=>'tab-pane-row' . (Yii::$app->user->isGuest ? '' : ' cp') . $class,
		];
	}
]);
?>