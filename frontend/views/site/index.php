<?php

/* @var $this yii\web\View */

$this->title = 'Библиотечный фонд Реаспекта';
use yii\helpers\Html;
use yii\widgets\Pjax;
/*
use yii\jui\AutoComplete;

echo AutoComplete::widget([
	'name' => 'book-search',
	'clientOptions' => [
		'source' => ['USA', 'RUS'],
	],
]);
*/
?>
<div class="container search-wrap">
	<div class="search-container row">
		<div class="search col-xs-12">
			<button type='button' class='close close-search hidden' data-dismiss='alert'>×</button>
			<input type="text" class="book-search form-control" placeholder="Поиск книги, начните набирать" autofocus />
		</div>
		<div class="adaptive-search col-xs-10">
			<button type='button' class='close close-search hidden' data-dismiss='alert'>×</button>
			<input type="text" class="book-search form-control" placeholder="Поиск книги, начните набирать" autofocus />
		</div>
		<div class="adaptive-menu-btn col-xs-1">
			<div class="adaptive-menu-btn-inner"></div>
		</div>
	</div>
</div>
<div class="container search-result hidden"></div>
<p></p>
<div class="container book-list show">
	<div class="list-tabs">
		<div class="bs-example bs-example-tabs">
			<ul id="myTab" class="nav nav-tabs">
				<li class="active"><a href="#debtors" data-toggle="tab">Должники</a></li>
				<li class=""><a href="#now-reading" data-toggle="tab">Сейчас читают</a></li>
				<li class=""><a href="#often-used" data-toggle="tab">Часто используемые</a></li>
				<li class=""><a href="#all-books" data-toggle="tab">Все книги</a></li><?php
				if(!\Yii::$app->user->isGuest) {
				?><li class="dropdown">
						<a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">Дополнительно<b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">
							<li class=""><a href="#options" tabindex="-1" data-toggle="tab">Настройки</a></li>
							<li class=""><a href="#add-book" tabindex="-1" data-toggle="tab">Добавить книгу</a></li>
							<li class=""><a href="#add-category" tabindex="-1" data-toggle="tab">Категории</a></li>
						</ul>
					</li><?php
				}
				?>
			</ul>
			<div id="myTabContent" class="tab-content">
				<div class="tab-pane fade active in" id="debtors"><?php
					Pjax::begin(['id' => 'grid-debtors']);
					echo $this->render('grid',
													array(
														'dataProvider' => $debtorsDataProvider,
														'columns' => $debtorsColumns,
													));
					Pjax::end();
				?></div>
				<div class="tab-pane fade" id="now-reading"><?php
					Pjax::begin(['id' => 'grid-now-reading']);
					echo $this->render('grid',
													array(
														'dataProvider' => $nowDataProvider,
														'columns' => $nowColumns,
													));
					Pjax::end();
				?></div>
				<div class="tab-pane fade" id="often-used"><?php
					Pjax::begin(['id' => 'grid-often-used']);
					echo $this->render('grid',
													array(
														'dataProvider' => $oftenDataProvider,
														'columns' => $oftenColumns,
													));
					Pjax::end();
				?></div>
				<div class="tab-pane fade" id="all-books"><?php
					Pjax::begin(['id' => 'grid-all-books']);
					echo $this->render('grid',
													array(
														'dataProvider' => $dataProvider,
														'columns' => $columns,
													));
					Pjax::end();
				?></div><?php
				if(!\Yii::$app->user->isGuest) {
				?><div class="tab-pane fade" id="options"><?php
					Pjax::begin(['id' => 'grid-options']);
					echo $this->render('options', array('model' => $options));
					Pjax::end();
				?></div>
				<div class="tab-pane fade" id="add-book"><?php
					Pjax::begin(['id' => 'grid-add-book']);
					echo $this->render('add-book', array('model' => $model));
					Pjax::end();
				?></div>
				<div class="tab-pane fade" id="add-category"><?php
					Pjax::begin(['id' => 'grid-add-book']);
					echo $this->render('add-category',
													array(
														'columns' => $categoriesColumns,
														'dataProvider' => $categoriesDataProvider,
													));
					Pjax::end();
				?></div><?php
				}
			?></div>
		</div>
	</div>
</div><?php
use yii\bootstrap\Modal;
Modal::begin([
	'header' => '<h2></h2>',
	'id' => 'modal-popup',
	'size' => 'modal-lg',
]);?><div id="modal-book-content">
		<div class="active progress-stripped progress">
			<div class="progress-bar progress-bar-success progress-bar-striped modal-loading" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">Загрузка...</div>
		</div>
	</div><?php
Modal::end();
?>
