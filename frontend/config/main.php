<?php

use frontend\modules\admin\Admin;
use frontend\modules\admin\controllers\AdminsController;
use frontend\modules\admin\controllers\DepartmentsController;
use frontend\modules\admin\controllers\SemestersController;
use frontend\modules\admin\controllers\StudentsController;
use frontend\modules\admin\controllers\SubjectsController;
use frontend\modules\admin\controllers\TeachersController;
use frontend\modules\teacher\controllers\MaterialsController;
use frontend\modules\teacher\controllers\QuizzesController;
use frontend\modules\teacher\controllers\TasksController;
use frontend\modules\teacher\Teacher;
use yii\filters\AccessControl;
use yii\log\FileTarget;
use yii\web\DbSession;
use yii\web\ForbiddenHttpException;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'admin' => [
            'class' => Admin::class,
        ],
        'teacher' => [
            'class' => Teacher::class,
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'tasks-frontend',
            'class' => DbSession::class,
            'timeout' => '604800',
            'useStrictMode' => true,
        ],
        'log' => [
            'traceLevel' => getenv('APP_ENV') === 'prod' ? 0 : 3,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'maskVars' => ['_SERVER', '_COOKIE', '_POST'],
                    'logFile' => '@logs/frontend.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => array_merge(
                //General
                \frontend\controllers\SiteController::getRoutes(),
                // Admin
                StudentsController::getRoutes(),
                TeachersController::getRoutes(),
                AdminsController::getRoutes(),
                SubjectsController::getRoutes(),
                DepartmentsController::getRoutes(),
                SemestersController::getRoutes(),
                // Teacher
                QuizzesController::getRoutes(),
                TasksController::getRoutes(),
                MaterialsController::getRoutes(),
                // Student
                \frontend\controllers\QuizzesController::getRoutes(),
                \frontend\controllers\TasksController::getRoutes(),
                \frontend\controllers\MaterialsController::getRoutes(),
            ),
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],

    ],
    'as beforeRequest' => [
        'class' => AccessControl::class,
        'rules' => [
            [
                'allow' => true,
                'actions' => ['login'],
                'roles' => ['?'],
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => static function ($rule, $action) {
            if (Yii::$app->user->isGuest) {
                return Yii::$app->response->redirect(['site/login']);
            }
            throw new ForbiddenHttpException('You are not allowed to access this page');
        },
    ],
    'params' => $params,
];
