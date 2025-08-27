<?php

namespace frontend\modules\admin\controllers;

use common\enum\PermissionType;
use common\models\Subjects;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SubjectsController implements the CRUD actions for Subjects model.
 */
class SubjectsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => [PermissionType::VIEW_SUBJECTS],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create','delete', 'update'],
                        'roles' => [PermissionType::ADD_SUBJECTS],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Subjects models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => Subjects::find()->with(['department', 'semester'])->all(),
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'description',
                    'credits',
                    'department.name' => [
                        'asc' => ['departments.name' => SORT_ASC],
                        'desc' => ['departments.name' => SORT_DESC],
                    ],
                    'semester.name' => [
                        'asc' => ['semesters.name' => SORT_ASC],
                        'desc' => ['semesters.name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Subjects model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Subjects model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Subjects();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Subjects model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Subjects model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Subjects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Subjects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Subjects::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public static function getRoutes(): array
    {
        return [
            'admin/subjects' => 'admin/subjects/index',
            'admin/subjects/create' => 'admin/subjects/create',
            'admin/subjects/<id>/update' => 'admin/subjects/update',
            'admin/subjects/<id>/delete' => 'admin/subjects/delete',
            'admin/subjects/<id>' => 'admin/subjects/view',
        ];
    }
}
