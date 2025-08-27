<?php

namespace api\modules\v1\controllers;

use common\enum\PermissionType;
use common\models\StudentDepartmentYear;
use common\models\User;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class UserController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/vnd.api+json' => Response::FORMAT_JSON,
                    ],
                ],
            ]
        );
    }
    public function actionAddStudents()
    {
        // check if the user sending the request has the permission to add students
        if (!Yii::$app->authManager->checkAccess(
            Yii::$app->session->get('user_id'),
            PermissionType::ADD_STUDENTS
        )) {
            return [
                'success' => false,
                'error' => 'Denied',
            ];
        }

        $payload = json_decode(Yii::$app->request->rawBody, true);
        if (empty($payload)) {
            return [
                'success' => false,
                'error' => 'Empty payload',
            ];
        }
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return [
                'success' => false,
                'error' => 'Invalid payload',
            ];
        }
        $failed = [];
        foreach ($payload as $student) {
            $studentModel = new User();
            $studentModel->name = $student['name'];
            $studentModel->email = $student['email'];
            if ($studentModel->save()) {
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole('students'), $studentModel->id);
                $studentDepartmentYear = new StudentDepartmentYear();
                $studentDepartmentYear->department_id = $student['department'];
                $studentDepartmentYear->semester_id = $student['semester'];
                $studentDepartmentYear->user_id = $studentModel->id;
                $studentDepartmentYear->save();
            } else {
                $failed[] = $student;
            }
        }
        if (empty($failed)) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
                'failed' => $failed,
            ];
        }
    }
}
