<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
<?php
	// Меню для desctop версии
	NavBar::begin([
		'brandLabel' => 'Библиотечный фонд Реаспект',
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar',
		],
	]);
	/*
	$menuItems = [
		['label' => 'Home', 'url' => ['/site/index']],
		['label' => 'About', 'url' => ['/site/about']],
		['label' => 'Contact', 'url' => ['/site/contact']],
	];
	*/
	$menuItems = [];
	if (Yii::$app->user->isGuest) {
		$menuItems[] = [
			'label' => 'Войти',
			//'url' => ['/site/login'],
			'linkOptions' => [
				'data-target' => '#myModal',
				'data-toggle' => 'modal',
				'id' => 'enter-modal',
			]
		];
    } else {
			$menuItems[] = [
				'label' => 'Выйти (' . Yii::$app->user->identity->username . ')',
				'url' => ['/site/logout'],
				'linkOptions' => ['data-method' => 'post'],
			];
		}
		echo Nav::widget([
			'options' => ['class' => 'navbar-nav navbar-right'],
			'items' => $menuItems,
		]);
		NavBar::end();
?><?php
	// Меню для мобильной версии
	NavBar::begin([
		'brandLabel' => 'Б<em>Ф</em>Р',
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'adaptive-navbar',
		],
	]);
	$menuItems = [];
	if (Yii::$app->user->isGuest) {
		$menuItems[] = [
			'label' => 'Войти',
			//'url' => ['/site/login'],
			'linkOptions' => [
				'data-target' => '#myModal',
				'data-toggle' => 'modal',
				'id' => 'enter-modal',
			]
		];
    } else {
			$menuItems[] = [
				'label' => 'Выйти (' . Yii::$app->user->identity->username . ')',
				'url' => ['/site/logout'],
				'linkOptions' => ['data-method' => 'post'],
			];
		}
		echo Nav::widget([
			'options' => ['class' => 'navbar-nav navbar-right'],
			'items' => $menuItems,
		]);
		NavBar::end();
?>
	<?= Alert::widget() ?>
	<?= $content ?>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Реаспект <?= date('Y') ?> <a href="http://www.reaspekt.ru/" target="_blank" class="" rel="nofollow">www.reaspekt.ru</a></p>
        <p class="pull-right">Powered by <a href="http://www.yiiframework.com/" target="_blank" class="" rel="nofollow">Yii Framework</a></p>
    </div>
</footer>
<?php $this->endBody() ?><?php
	if(!\Yii::$app->user->isGuest) {
		?><script src="js/admin.js"></script><?php
	}
?></body>
</html>
<?php $this->endPage() ?>
