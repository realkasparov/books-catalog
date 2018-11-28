<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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
     * {@inheritdoc}
     */
    public function actions()
    {
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
     * @return string
     */
    public function actionIndex()
    {
        $bookModel = new Book();
        $books = $bookModel::find()->all();
        $authorModel = new Author();
        $authors = $authorModel::find()->all();

        $booksData = [];
        foreach ($books as $book) {
            $booksData[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => implode(', ', ArrayHelper::map($bookModel::findOne($book->id)->bookAuthors, 'name', 'name')),
            ];
        }
        $authorsData = [];
        foreach ($authors as $author) {
            $authorsData[] = [
                'id' => $author->id,
                'name' => $author->name,
                'bookCount' => count(ArrayHelper::map($authorModel::findOne($author->id)->bookAuthors, 'name', 'name')),
            ];
        }

        $dataProviderBooks = new ArrayDataProvider([
            'key'=>'id',
            'allModels' => $booksData,
            'sort' => [
                'attributes' => ['id', 'name', 'author'],
            ],
        ]);
        $dataProviderAuthors = new ArrayDataProvider([
            'key'=>'id',
            'allModels' => $authorsData,
            'sort' => [
                'attributes' => ['id', 'name', 'bookCount'],
            ],
        ]);

        return $this->render('index', [
            'dataProviderBooks' => $dataProviderBooks,
            'dataProviderAuthors' => $dataProviderAuthors,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
