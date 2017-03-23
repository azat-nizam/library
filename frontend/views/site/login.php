<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
	<p>Please fill out the following fields to login:</p>
	<div class="row">
		<div class="col-lg-5"><?php $form = ActiveForm::begin([
						'id' => 'modal-book',
						'action' => '/site/login',
					]);
				echo $form->field($model, 'username');
				echo $form->field($model, 'password')->passwordInput();
				echo $form->field($model, 'rememberMe')->checkbox();
				?>
                <div class="form-group"><?php
				echo Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']);
				?></div><?php
			ActiveForm::end();
			?></div>
    </div>
</div>