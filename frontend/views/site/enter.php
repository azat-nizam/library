<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<?php $form = ActiveForm::begin(['id' => 'login-form']);
	echo $form->field($model, 'username');
	echo $form->field($model, 'password')->passwordInput();
	echo $form->field($model, 'rememberMe')->checkbox();
?><div class="modal-footer"><?php
		echo Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'enter-button']);
		echo Html::button('Закрыть', ['class' => 'btn btn-default', 'name' => 'close-button', 'data-dismiss' => 'modal']);
	ActiveForm::end();
?>