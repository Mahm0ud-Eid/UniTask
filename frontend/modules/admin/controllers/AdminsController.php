<?php

namespace frontend\modules\admin\controllers;

use common\models\User;
use common\enum\PermissionType;
use frontend\modules\admin\models\search\AdminsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use frontend\modules\admin\models\admins\AdminFormModel;

/**
 * TeachersController implements the CRUD actions for User model.
 */
class AdminsController extends Controller
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
                        'actions' => ['index'],
                        'roles' => [PermissionType::VIEW_STUDENTS],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create' ,'delete', 'update'],
                        'roles' => [PermissionType::ADD_STUDENTS],
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
        $searchModel = new AdminsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new AdminFormModel();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->add()) {
                return $this->redirect(['/admin/admins']);
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
        $model = new AdminFormModel();
        $model->loadUser($user);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->update()) {
                return $this->redirect(['/admin/admins']);
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

    /**
     * @return string[]
     */
    public static function getRoutes(): array
    {
        return [
            'admin/admins' => 'admin/admins/index',
            'admin/admins/create' => 'admin/admins/create',
            'admin/admins/<id>/update' => 'admin/admins/update',
            'admin/admins/<id>/delete' => 'admin/admins/delete',
        ];
    }
}
