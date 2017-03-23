<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
/**
 * Options Model
 *
 * @property integer $id
 * @property integer $duration
 */
class Options extends ActiveRecord {
	/**
	* @inheritdoc
	*/
	public static function tableName() {
		return 'options';
	}
	/**
	* @inheritdoc
	*/
	public function rules() {
		return [
			[['id', 'duration',], 'safe'],
		];
	}
	/**
	* @inheritdoc
	*/
	public function attributeLabels() {
		return [
			'id' => 'Настройки',
			'duration' => 'Продолжительность чтения, дней',
		];
	}
	public function getOptions() {
		$options = self::findOne(1);
		$data = array();
		$data['id'] = $options->id;
		$data['duration'] = $options->duration;
		return $data;
	}
}