<?php

namespace app\controllers;

use app\models\Author;
use app\models\BookAuthor;
use Yii;
use app\models\Book;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Book();
        $books = $model::find()->all();

        $resultData = [];
        foreach ($books as $book) {
            $resultData[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => implode(', ', ArrayHelper::map($model::findOne($book->id)->bookAuthors, 'name', 'name')),
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'key'=>'id',
            'allModels' => $resultData,
            'sort' => [
                'attributes' => ['id', 'name', 'author'],
            ],
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $result = [
            'id' => $model->id,
            'name' => $model->name,
            'authors' => implode(', ', ArrayHelper::map($model->bookAuthors, 'name', 'name'))
        ];

        return $this->render('view', [
            'model' => $result,
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $book = new Book();
        $authors = Author::find()->all();
        $authorsList = ArrayHelper::map($authors, 'id', 'name');

        $post = Yii::$app->request->post();
        if ($book->load($post) && $book->save()) {
            if (isset($post["Book"]["authors"]) && is_array($post["Book"]["authors"])) {
                foreach ($post["Book"]["authors"] as $author) {
                    BookAuthor::create($book_id = $book->id, $author_id = (int)$author);
                }
            }
            return $this->redirect(['view', 'id' => $book->id]);
        }

        return $this->render('create', [
            'model' => $book,
            'authors' => $authors,
            'authorsList' => $authorsList,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $book = $this->findModel($id);
        $book->authors = array_map(function(Author $author) {
            return $author->id;
        }, $book->bookAuthors);
        $authors = Author::find()->all();
        $authorsList = ArrayHelper::map($authors, 'id', 'name');

        $post = Yii::$app->request->post();
        if ($book->load($post) && $book->save()) {
            if (isset($post["Book"]["authors"]) && is_array($post["Book"]["authors"])) {
                foreach ($post["Book"]["authors"] as $author) {
                    BookAuthor::create($book_id = $book->id, $author_id = (int)$author);
                }
            }
            return $this->redirect(['view', 'id' => $book->id]);
        }

        return $this->render('update', [
            'model' => $book,
            'authors' => $authors,
            'authorsList' => $authorsList,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
