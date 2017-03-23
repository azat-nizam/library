<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
/**
 * Reading Model
 *
 * @property integer $id
 * @property integer $bookId
 * @property string $reader
 * @property timestamp $startDate
 */
class Reading extends ActiveRecord {
	/**
	* @inheritdoc
	*/
	public static function tableName() {
		return 'reading';
	}
	/**
	* @inheritdoc
	*/
	public function rules() {
		return [
			[['id', 'bookId', 'reader', 'startDate',], 'safe'],
		];
	}
	/**
	* @inheritdoc
	*/
	public function attributeLabels() {
		return [
			'id' => 'Чтение',
			'bookId' => 'Книга',
			'reader' => 'Читатель',
			'startDate' => 'Дата взятия',
		];
	}
}