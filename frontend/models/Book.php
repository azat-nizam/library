<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
/**
 * Book Model
 *
 * @property integer $id
 * @property integer $categoryId
 * @property string $author
 * @property string $name
 * @property boolean $status
 * @property integer $count
 * @property timestamp $startDate
 * @property timestamp $endDate
 * @property string $reader
 * @property boolean $inaccessible
 */
class Book extends ActiveRecord {
	const BOOK_FREE = '0';
	const BOOK_BUSY = '1';
	const BOOK_INACCESSIBLE = '2';
	const BOOK_FREE_TEXT = '<span>Доступна</span>';
	const BOOK_BUSY_TEXT = '<span>Занята</span>';
	const BOOK_INACCESSIBLE_TEXT = '<span>Отсутствует</span>';
	//const BOOK_FREE_TEXT_GUEST = 'Доступна';
	//const BOOK_BUSY_TEXT_GUEST = 'Занята';
	//const BOOK_INACCESSIBLE_TEXT_GUEST = 'Отсутствует';
	/**
	* @inheritdoc
	*/
	public static function tableName() {
		return 'book';
	}
	/**
	* @inheritdoc
	*/
	public function rules() {
		return [
			[['id', 'categoryId', 'author', 'status', 'count', 'startDate', 'endDate', 'reader', 'inaccessible'], 'safe'],
			[['name',],'required'],
		];
	}
	/**
	* @inheritdoc
	*/
	public function attributeLabels() {
		return [
			'id' => 'Книга',
			'categoryId' => 'Категория',
			'author' => 'Автор',
			'name' => 'Название',
			'status' => 'Статус',
			'count' => 'Счетчик',
			'startDate' => 'Получена',
			'endDate' => 'Возврат',
			'reader' => 'Читатель',
			'inaccessible' => 'Книга не доступна',
		];
	}
	private function _formStatusContentOptions($model, $key, $index, $column, $bootstrap) {
		$class = $bootstrap;
		switch($model['status']) {
			case self::BOOK_FREE:
				$class = 'book-free';
				break;
			case self::BOOK_BUSY:
				$class = 'book-busy';
				break;
			case self::BOOK_INACCESSIBLE:
				$class = 'book-inaccessible';
				break;
			default:
				$class = 'book-busy';
		}
		return [
			'class' => 'col-xs-1 ' . $class . (Yii::$app->user->isGuest ? '' : ' ajax-btn'),
			'data-id' => $model['id'],
		];
	}
	private function _formStatusContent($data) {
		$value = '';
		switch($data['status']) {
			case self::BOOK_FREE:
				$value = self::BOOK_FREE_TEXT;
				break;
			case self::BOOK_BUSY:
				$value = self::BOOK_BUSY_TEXT;
				break;
			case self::BOOK_INACCESSIBLE:
				$value = self::BOOK_INACCESSIBLE_TEXT;
				break;
			default:
				$value = self::BOOK_BUSY_TEXT;
		}
		return $value;
	}
	/**
	 * Поиск должников, книги сейчас читают (статус не равен BOOK_FREE),
	 * и с просроченной датой возврата.
	 */
	public function searchDebtorsBooks() {
		
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		$query
			-> select(['*']) -> from(self::tableName())
			-> where('status=:status', [':status' => self::BOOK_BUSY])
			-> andWhere('UNIX_TIMESTAMP(endDate) < UNIX_TIMESTAMP()')
			-> andWhere('UNIX_TIMESTAMP(startDate) < UNIX_TIMESTAMP(endDate)')
			-> orderBy('reader ASC')
		;
		$columns = [
			[
				'attribute' => 'author',
				'label' => 'Автор',
				'contentOptions' => [
					'class' => 'col-xs-2 hidden-xs',
				],
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-6',
				],
			],
			[
				'attribute' => 'reader',
				'label' => 'Читатель',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
				'content' => function($data) {
					return str_replace('+', ' ', $data['reader']);
				},
			],
			[
				'attribute' => 'endDate',
				'label' => 'Возврат',
				'contentOptions' => [
					'class' => 'col-xs-1 date-expired',
				],
				'content' => function($data) {
					$time = strtotime($data['endDate']);
					$date = date('d.m.Y', $time);
					return $date;
				},
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'contentOptions' => function($model, $key, $index, $column) {
					return $this->_formStatusContentOptions($model, $key, $index, $column, 'hidden-xs');
				},
				'content' => function($data) {
					return $this->_formStatusContent($data);
				},
			],
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
	/**
	 * Поиск книг, которые читают сейчас, статус равен BOOK_BUSY
	 */
	public function searchNowReading() {
		
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		$query
			-> select(['*'])
			-> from(self::tableName())
			-> where('status=:status', [':status' => self::BOOK_BUSY])
			-> orderBy('reader ASC')
		;
		$columns = [
			[
				'attribute' => 'author',
				'label' => 'Автор',
				
				'contentOptions' => [
					'class' => 'col-xs-2 hidden-xs',
				],
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-6',
				],
			],
			[
				'attribute' => 'reader',
				'label' => 'Читатель',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
				'content' => function($data) {
					return str_replace('+', ' ', $data['reader']);
				},
			],
			[
				'attribute' => 'endDate',
				'label' => 'Возврат',
				'contentOptions' => function($data){
					$time = strtotime($data['endDate']);
					$now = time();
					return [
						'class' => 'col-xs-1' . ($time < $now ? ' date-expired' : ''),
					];
				},
				'content' => function($data) {
					$time = strtotime($data['endDate']);
					$date = date('d.m.Y', $time);
					return $date;
				},
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'contentOptions' => function($model, $key, $index, $column) {
					return $this->_formStatusContentOptions($model, $key, $index, $column, 'hidden-xs');
				},
				'content' => function($data) {
					return $this->_formStatusContent($data);
				},
			],
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
	/**
	 * Поиск часто читаемых книг
	 */
	public function oftenUsed() {
		
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		$query
				-> select(['*'])
				-> from(self::tableName())
				-> orderBy('count DESC, author ASC, name ASC')
				-> limit(10)
		;
		$columns = [
			[
				'attribute' => 'author',
				'label' => 'Автор',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-9',
				],
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'contentOptions' => function($model, $key, $index, $column) {
					return $this->_formStatusContentOptions($model, $key, $index, $column, '');
				},
				'content' => function($data) {
					return $this->_formStatusContent($data);
				},
			],
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
	/**
	 * Поиск всех книг
	 */
	public function search() {
		
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		$query
				-> select(['*'])
				-> from(self::tableName())
				-> orderBy('author ASC, name ASC')
		;
		$columns = [
			[
				'attribute' => 'author',
				'label' => 'Автор',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-9',
				],
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'contentOptions' => function($model, $key, $index, $column) {
					return $this->_formStatusContentOptions($model, $key, $index, $column,'');
				},
				'content' => function($data) {
					return $this->_formStatusContent($data);
				},
			],
			/*
			'rowOptions'=>function ($model, $key, $index, $grid) {
				$class=$index%2?'odd':'even';
				return [
					'key'=>$key,
					'index'=>$index,
					'class'=>$class
				];
			}
			*/
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
	/**
	 * Ищет книгу в базе по названию и автору в соответствии с запросом
	 */
	public function searchBook($request) {
		
		$query = new \yii\db\Query();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);
		if($request == '') {
			$rows = $query
						-> select(['*'])
						-> from(self::tableName())
						-> orderBy('author ASC, name ASC')
						-> limit(10)
						-> all()
						;
		} else {
			$rows = $query
						-> select(['*'])
						-> from(self::tableName())
						-> where(['like', 'author', $request])
						-> orWhere(['like', 'name', $request])
						-> orderBy('author ASC, name ASC')
						-> all()
						;
		}

		$columns = [
			[
				'attribute' => 'author',
				'label' => 'Автор',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
			],
			[
				'attribute' => 'name',
				'label' => 'Название',
				'contentOptions' => [
					'class' => 'col-xs-6',
				],
			],
			[
				'attribute' => 'reader',
				'label' => 'Читатель',
				'contentOptions' => [
					'class' => 'col-xs-2',
				],
				'content' => function($data) {
					return str_replace('+', ' ', $data['reader']);
				},
			],
			[
				'attribute' => 'endDate',
				'label' => 'Возврат',
				'contentOptions' => function($data){
					$time = strtotime($data['endDate']);
					$now = time();
					return [
						'class' => 'col-xs-1' . ($time < $now ? ' date-expired' : ''),
					];
				},
				'content' => function($data) {
					if($data['endDate'] == '0000-00-00 00:00:00'){
						$date = '';
					} else {
						$time = strtotime($data['endDate']);
						$date = date('d.m.Y', $time);
					}
					return $date;
				},
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'contentOptions' => function($model, $key, $index, $column) {
					return $this->_formStatusContentOptions($model, $key, $index, $column,'');
				},
				'content' => function($data) {
					return $this->_formStatusContent($data);
				},
			],
		];
		return [
			'columns' => $columns,
			'dataProvider' => $dataProvider,
		];
	}
	/**
	 * Книга взята на чтение
	 */
	public function takeBook($bookId, $reader) {
		// получаем настройки
		$optionsModel = new Options();
		$options = $optionsModel->getOptions();
		$days = $options['duration'];
		// ищем книгу по идентификатору
		$book = self::findOne($bookId);
		$book -> status = self::BOOK_BUSY;
		$book -> reader = $reader;
		$startDate = date('Y-m-d H:i:s', time());
		$endDate = date('Y-m-d 18:30:00', strtotime('+' . $days . ' days'));
		$book -> startDate = $startDate;
		$book -> endDate = $endDate;
		$book -> count = ($book -> count) + 1;
		$book -> save();
		return self::BOOK_BUSY_TEXT;
	}
	/**
	 * Книга возвращена
	 */
	public function returnBook($bookId) {
		$book = self::findOne($bookId);
		$book -> status = self::BOOK_FREE;
		$book -> reader = '';
		$book -> startDate = '0000-00-00 00:00:00';
		$book -> endDate = '0000-00-00 00:00:00';
		$book -> save();
		return self::BOOK_FREE_TEXT;
	}
	/**
	 * Получить список категорий
	 */
	public function getCategory() {
		$category = new Category();
		$categories = $category->getCategory();
		return $categories;
	}
}
