<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<?php
	/*
	$this->registerJs(
		'$("document").ready(function(){
				$("#book-work").on("pjax:end", function() {
					$.pjax.reload({container:"#' . $grid . '"});
				});
		});'
	);
	
	Pjax::begin(['id' => 'book-work']);
		$form = ActiveForm::begin([
							'action' => '/',
							'id' => 'hidden-book',
							'options' => [
								'data-pjax' => true,
							],
						]);
			echo $form->field($model, 'id')->hiddenInput()->label(false);
		ActiveForm::end();
	Pjax::end();
	*/
	Pjax::begin();
	$form = ActiveForm::begin([
							'id' => 'modal-book',
							'options' => [
								'data-form' => $grid,
								//'data-pjax' => '1',
							],
							'fieldConfig' => [
								'labelOptions' => [
									//'class' => 'col-xs-6',
								],
							],
						]);
		echo $form->field($model, 'id')->hiddenInput()->label(false);
		echo $form->field($model, 'status')->hiddenInput()->label(false);
		echo $form->field($model, 'count')->hiddenInput()->label(false);
		echo $form->field($model, 'author');
		echo $form->field($model, 'name');
		echo $form->field($model, 'categoryId')->dropDownList($model->getCategory());
		echo $form->field($model, 'reader');
		?>
		<input type="hidden" value="<?=$days;?>" id="reading-time" />
		<div class="row"><div class="col-xs-6"><?php
		echo $form->field($model, 'startDate')->widget(DatePicker::classname(), [
			'language' => 'ru',
			'dateFormat' => 'yyyy-MM-dd',
			'options' => [
				'class' => 'form-control',
			],
		]);?></div>
		<div class="col-xs-6"><?php
		echo $form->field($model, 'endDate')->widget(DatePicker::classname(), [
			'language' => 'ru',
			'dateFormat' => 'yyyy-MM-dd',
			'options' => [
				'class' => 'form-control col-xs-6',
			],
		]);
		?></div></div><?php
		echo $form->field($model, 'inaccessible')->checkbox();
		?><div class="modal-footer"><?php
				echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']);
				echo Html::button('Закрыть', ['class' => 'btn btn-default', 'name' => 'close-button', 'data-dismiss' => 'modal']);
		?></div><?php
	ActiveForm::end();
	Pjax::end();
?>