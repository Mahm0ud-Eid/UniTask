<?php

namespace frontend\modules\admin\controllers;

use common\models\StudentDepartmentYear;
use common\models\User;
use frontend\modules\admin\models\search\StudentsSearch;
use common\enum\PermissionType;
use frontend\modules\admin\models\students\AddFormModel;
use frontend\modules\admin\models\students\UpdateFormModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StudentsController implements the CRUD actions for User model.
 */
class StudentsController extends Controller
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
                        'actions' => ['create','delete', 'update'],
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
        $searchModel = new StudentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new AddFormModel();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->add()) {
                return $this->redirect(['/admin/students']);
            } else {
                Yii::error('Failed to save data: ' . print_r($model->errors, true));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        $user = User::findOne($id);
        $departmentYear = StudentDepartmentYear::findOne(['user_id' => $id]);
        if (!$user || !$departmentYear) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = new UpdateFormModel();
        $model->id = $user->id;
        $model->name = $user->name;
        $model->email = $user->email;
        $model->department_id = $departmentYear->department_id;
        $model->semester_id = $departmentYear->semester_id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->update()) {
                return $this->redirect(['/admin/students']);
            } else {
                Yii::error('Failed to update data: ' . print_r($model->errors, true));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * @return string[]
     */
    public static function getRoutes(): array
    {
        return [
            'admin/students' => 'admin/students/index',
            'admin/students/create' => 'admin/students/create',
            'admin/students/<id>/delete' => 'admin/students/delete',
            'admin/students/<id>/update' => 'admin/students/update',
        ];
    }
}
