<?php

namespace frontend\modules\admin\controllers;

use common\models\User;
use frontend\modules\admin\models\search\TeachersSearch;
use common\enum\PermissionType;
use frontend\modules\admin\models\teachers\AddTeacherFormModel;
use frontend\modules\admin\models\teachers\UpdateTeacherFormModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TeachersController implements the CRUD actions for User model.
 */
class TeachersController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'roles' => [PermissionType::VIEW_TEACHERS],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create' ,'delete', 'update'],
                        'roles' => [PermissionType::ADD_TEACHERS],
                    ],
                ],
            ],

        ];
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeachersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new AddTeacherFormModel();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->add()) {
                Yii::info('Data saved successfully: ' . print_r($model->attributes, true));
                return $this->redirect(['/admin/teachers']);
            } else {
                Yii::error('Failed to save data: ' . print_r($model->errors, true));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new UpdateTeacherFormModel();
        $model->loadUser($user);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->update()) {
                Yii::$app->session->setFlash('success', 'Data saved successfully');
                return $this->redirect(['/admin/teachers']);
            } else {
                Yii::error('Failed to save data: ' . print_r($model->errors, true));
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['/admin/teachers']);
    }

    public function actionView($id)
    {
        // view user data and his subjects
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string[]
     */
    public static function getRoutes(): array
    {
        return [
            'admin/teachers' => 'admin/teachers/index',
            'admin/teachers/create' => 'admin/teachers/create',
            'admin/teachers/<id>/update' => 'admin/teachers/update',
            'admin/teachers/<id>/delete' => 'admin/teachers/delete',
            'admin/teachers/<id>/view' => 'admin/teachers/view',
        ];
    }
}
