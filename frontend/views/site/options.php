<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin(['id' => 'modal-options']);
	echo $form->field($model, 'id')->hiddenInput()->label(false);
	echo $form->field($model, 'duration');
	?><div class="modal-footer"><?php
			echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']);
	?></div>
<?php
ActiveForm::end();
return;
?>
<form role="form">
	<div class="form-group">
		<label for="reading-time">Длительность чтения, дней</label>
		<input type="text" class="form-control" id="reading-time" placeholder="Длительность чтения, дней" value="14">
	</div>
	<button type="submit" class="btn btn-primary">Отправить</button>
</form>