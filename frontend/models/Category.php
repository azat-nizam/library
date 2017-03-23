<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
/**
 * Category Model
 *
 * @property integer $id
 * @property string $name
 */
class Category extends ActiveRecord {
	/**
	* @inheritdoc
	*/
	public static function tableName() {
		return 'category';
	}
	/**
	* @inheritdoc
	*/
	public function rules() {
		return [
			[['id',], 'safe'],
			[['name',], 'required'],
		];
	}
	/**
	* @inheritdoc
	*/
	public function attributeLabels() {
		return [
			'id' => 'Категория',
			'name' => 'Название',
		];
	}
	/**
	 * Возвращает список всех категорий
	 */
	public function getCategory() {
		$query = new \yii\db\Query();
		$rows = $query
						-> select(['*'])
						-> from(self::tableName())
						-> orderBy('name ASC')
						-> all()
						;
		$data = array();
		foreach($rows as $row) {
			$data[$row['id']] = $row['name'];
		}
		return $data;
	}
	
	public function searchCategories() {
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		$query
				-> select(['*'])
				-> from(self::tableName())
				-> orderBy('name ASC')
				-> all()
		;
		$columns = [
			[
				'attribute' => 'id',
				'label' => 'Категория',
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-12 editable-field',
				],
				'content' => function($data) {
					return '<span>' . $data['name'] . '</span>';
				},
			],
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
}