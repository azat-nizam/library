<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<?php $form = ActiveForm::begin([
	'id' => 'modal-book',
	'action' => '/site/addbook',
]);
	echo $form->field($model, 'author');
	echo $form->field($model, 'name');
	echo $form->field($model, 'categoryId')->dropDownList($model->getCategory());
	echo Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'login-button']);
	?><span class="response"></span><?php
ActiveForm::end();
?>