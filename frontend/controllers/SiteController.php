<?php
namespace frontend\controllers;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\Book;
use frontend\models\Category;
use frontend\models\Reading;
use frontend\models\Options;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * Site controller
 */
class SiteController extends Controller {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout', 'signup'],
				'rules' => [
					[
						'actions' => ['signup'],
						'allow' => true,
						'roles' => ['?'],
					],
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}
	/**
	 * Displays homepage.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$options = Options::findOne(1);
		// сохранение настроек приложения
		if(isset($_POST['Options'])) {
			$options->attributes = $_POST['Options'];
			if($options->validate()) {
				if($options->save()) {
					
				} else {
					// не удалось сохранить данные
				}
			} else {
				// не валидные данные
			}
		}
		$model = new Book();
		$often = $model->oftenUsed();
		$debtors = $model->searchDebtorsBooks();
		$nowReading = $model->searchNowReading();
		$data = $model->search();
		$category = new Category();
		$categories = $category->searchCategories();
		return $this->render(
			'index',
			array(
				'options' => $options,
				'model' => $model,
				// часто используемые
				'oftenColumns' => $often['columns'],
				'oftenDataProvider' => $often['dataProvider'],
				// должники
				'debtorsColumns' => $debtors['columns'],
				'debtorsDataProvider' => $debtors['dataProvider'],
				// сейчас читают
				'nowColumns' => $nowReading['columns'],
				'nowDataProvider' => $nowReading['dataProvider'],
				// все книги
				'columns' => $data['columns'],
				'dataProvider' => $data['dataProvider'],
				// список категорий
				'categoriesColumns' => $categories['columns'],
				'categoriesDataProvider' => $categories['dataProvider'],
			)
		);
	}
	/**
	 * Резервирует/снимает с резервации книги
	 * ajax метод
	 */
	public function actionBook() {
		if(isset($_POST['id']) && isset($_POST['action'])) {
			$id = $_POST['id'];
			$action = $_POST['action'];
			$model = new Book();
			$result = '';
			if($action == 'reserve') {
				$result = $model->takeBook($id, 'test');
			} elseif($action == 'unreserve') {
				$result = $model->returnBook($id);
			}
			echo $result;
		}
	}
	public function actionSearchfirst(){
		$model = new Book();
		$data = $model->searchBook('');
		echo '<div id="grid-search">';
		echo $this->renderAjax(
			'grid',
			array(
				'columns' => $data['columns'],
				'dataProvider' => $data['dataProvider'],
			)
		);
		echo '</div>';
	}
	/**
	 * Поиск книг в базе данных
	 * ajax метод
	 */
	public function actionSearch() {
		$booksList = array();
		if(isset($_POST['name'])) {
			$query = rawurldecode($_POST['name']);
			$Book = new Book();
			$data = $Book->searchBook($query);
			echo '<div id="grid-search">';
			echo $this->renderAjax(
				'grid',
				array(
					'columns' => $data['columns'],
					'dataProvider' => $data['dataProvider'],
				)
			);
			echo '</div>';
		}
		echo '';
	}
	/**
	 * Отображает окно для редактирования/резервирования книги
	 */
	public function actionModal() {
		$model = new Book();
		if(isset($_POST['d'])) {
			$result = '';
			$d = $_POST['d'];
			//$data=unserialize($_POST['d']);
			$data = rawurldecode($d);
			$data = explode('&', $data);
			$data3 = array();
			foreach($data as $data2) {
				$arr = explode('=', $data2);
				$data3[$arr[0]] = $arr[1];
			}
			$data = $data3;
			$id = $data['Book[id]'];
			$author = str_replace('+', ' ', $data['Book[author]']);
			$name =  str_replace('+', ' ', $data['Book[name]']);
			$categoryId = $data['Book[categoryId]'];
			$count = $data['Book[count]'];
			$status = $data['Book[status]'];
			$reader = str_replace('+', ' ', $data['Book[reader]']);
			/*
			if($reader == '') {
				$startDate = '0000-00-00 00:00:00';
				$endDate = '0000-00-00 00:00:00';
			} else {
				$startDate = $data['Book[startDate]'];
				$endDate = $data['Book[endDate]'];
			}
			*/
			$inaccessible = $data['Book[inaccessible]'];
			
			$model = Book::findOne($id);
			if($data['Book[reader]']=='') {
				// обновляем данные о книге
				$status = Book::BOOK_FREE;
			} else {
				// не увеличиваем счетчик использования для книги, которую читают
				if($model->status != BOOK_BUSY) {
					++$count;
				}
				// ставим книгу в чтение
				$status = Book::BOOK_BUSY;
			}
			if($data['Book[inaccessible]']!=0) {
				$status = Book::BOOK_INACCESSIBLE;
				$reader = '';
				$model->startDate = '0000-00-00 00:00:00';
				$model->endDate = '0000-00-00 00:00:00';
			}
			$model->author = $author;
			$model->name = $name;
			$model->categoryId = $categoryId;
			$model->status = $status;
			$model->count = $count;
			$model->reader = $reader;
			$model->startDate = $reader==''?$model->startDate:$data['Book[startDate]'];
			$model->endDate = $reader==''?$model->endDate:$data['Book[endDate]'];
			$model->inaccessible = $inaccessible;
			/*
			Возможна ситуация что при сохранении можно пересохранить другому читателю
			*/
			if($model->validate()) {
				if($model->save()) {
					$result = '<div class="error">Данные сохранены</div>';
				} else {
					$result = '<div class="error">Ошибка при сохранении</div>';
				}
			} else {
				$result = '<div class="error">Данные не валидны</div>';
			}
			echo $result;
		}
		else {
			$model = $model->findOne($_GET['id']);
			$model->startDate = $model->startDate=='0000-00-00 00:00:00'?date('Y-m-d H:i:s'):$model->startDate;
			// получаем настройки
			$optionsModel = new Options();
			$options = $optionsModel->getOptions();
			$days = $options['duration'];
			$model->endDate = $model->endDate=='0000-00-00 00:00:00'?date('Y-m-d 18:30:00', strtotime('+' . $days . ' days')):$model->endDate;
			$model->reader = str_replace('+', ' ', $model->reader);
			$grid = isset($_GET['g']) ? $_GET['g'] : '';
			return $this->renderAjax(
				'modal',
				array(
					'model' => $model,
					'grid' => $grid,
					'days' => $days,
				)
			);
		}
	}
	
	public function actionEnter() {
		if(isset($_POST['d'])) {
			$result = '';
			$d = $_POST['d'];
			$data = rawurldecode($d);
			$data = explode('&', $data);
			$data3 = array();
			foreach($data as $data2) {
				$arr = explode('=', $data2);
				$data3[$arr[0]] = $arr[1];
			}
			$data = $data3;
			//var_dump($data);
			if($data['exampleInputEmail1'] == 'test@test.ru' && $data['exampleInputPassword1'] == 'test') {
				Yii::$app->user->login($data['exampleInputEmail1'], $this->rememberMe ? 3600 * 24 * 30 : 0);
			} else {
				return false;
			}
		}
		//return $this->renderAjax('modal', array());
	}
	/**
	 * Logs in a user.
	 *
	 * @return mixed
	 */
	public function actionLogin() {
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		$model = new LoginForm();
		if(isset($_POST['d'])) {
			$d = $_POST['d'];
			$data = rawurldecode($d);
			$data = explode('&', $data);
			$data3 = array();
			foreach($data as $data2) {
				$arr = explode('=', $data2);
				$data3[$arr[0]] = $arr[1];
			}
			$data = $data3;
			$model->username = $data['LoginForm[username]'];
			$model->password = $data['LoginForm[password]'];
			$model->rememberMe = $data['LoginForm[rememberMe]'];
		}
		if (/*$model->load(Yii::$app->request->post()) && */$model->login()) {
			return $this->goBack();
		} else {
			return $this->renderAjax('login', [
				'model' => $model,
			]);
		}
	}
	/**
	 * Logs out the current user.
	 *
	 * @return mixed
	 */
	public function actionLogout() {
		Yii::$app->user->logout();
		return $this->goHome();
	}

	
	public function actionAddbook() {
		$model = new Book();
		$result = array();
		$result = '<span class="alert alert-warning">Не корректные данные</span>';
		if(isset($_POST['d'])) {
			$d = $_POST['d'];
			$data = rawurldecode($d);
			$data = explode('&', $data);
			$data3 = array();
			foreach($data as $data2) {
				$arr = explode('=', $data2);
				$data3[$arr[0]] = $arr[1];
			}
			$data = $data3;
			$model = new Book();
			$model->author = str_replace('+', ' ' , $data['Book[author]']);
			$model->name = str_replace('+', ' ' , $data['Book[name]']);
			$model->categoryId = $data['Book[categoryId]'];
			//$model->attributes = $_POST['Book'];
			$model->status = Book::BOOK_FREE;
			$model->count = 0;
			$model->inaccessible = 0;
			$model->reader = '';
			if($model->validate()) {
				if($model->save()) {
					// данные сохранены
					$result = '<span class="alert alert-success">Данные успешно сохранены</span>';
				} else {
					// не удалось сохранить
					$result = '<span class="alert alert-warning">Не удалось сохранить данные';
				}
			} else {
				// данные не валидны
				$result = '<span class="alert alert-warning">Не все поля заполнены</span>';
			}
		}
		echo $result;
	}
	
	public function actionAddcategory() {
		if(isset($_POST['i'])) {
			$id = $_POST['i'];
			if($id == '') {
				$model = new Category();
			} else {
				$model = Category::findOne($id);
			}
		}
		if(isset($_POST['n'])) {
			$name = $_POST['n'];
		}
		$model->name = $name;
		$result = array('status' => 'error');
		if($name == '') {
				// удаляем категорию
				if($model->delete()) {
					$result['status'] = 'data deleted';
				} else {
					$result['status'] = 'unable to delete';
				}
				// убираем категорию и книг
				//Book::
			} else {
				if($model->validate()) {
					if($model->save()) {
						$result['status'] = 'data saved';
					} else {
						$result['status'] = 'unable to save';
					}
				} else {
					$result['status'] = 'unable to validate';
				}
			}
		echo json_encode($result);
	}
	/**
	 * Requests password reset.
	 *
	 * @return mixed
	 */
	public function actionRequestPasswordReset() {
		$model = new PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
				return $this->goHome();
			} else {
				Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
			}
		}
		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
	}
	/**
	 * Resets password.
	 *
	 * @param string $token
	 * @return mixed
	 * @throws BadRequestHttpException
	 */
	public function actionResetPassword($token) {
		try {
			$model = new ResetPasswordForm($token);
		} catch (InvalidParamException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}
		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->session->setFlash('success', 'New password was saved.');
			return $this->goHome();
		}
		return $this->render('resetPassword', [
			'model' => $model,
		]);
	}
    
    /**
     * Dev tool. Get password hash. Close this lines in production version
     */
	/*public function actionTool() {
		echo "<p>Set new password</p>";
		
		echo Yii::$app->user->getId();
		echo "<br />" . Yii::$app->security->generatePasswordHash("password-string");
	}*/
}
