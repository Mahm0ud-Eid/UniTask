<?php

use api\modules\v1\APIv1;
use common\models\User;
use yii\log\FileTarget;

if (getenv('FORCE_HTTPS') === 'on') {
    $_SERVER['HTTPS'] = 'on';
}
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'name' => 'API',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => APIv1::class,
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'trustedHosts' => [
                '10.0.0.0/8',
                '172.19.0.0/16',
                '172.20.0.0/16',
                '172.18.0.0/16',
            ],
        ],
        'user'    => [
            'identityClass'   => User::class,
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'api',
            'class' => 'yii\web\CacheSession',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => array_merge(
                APIv1::getRoutes(),
            ),
        ],
        'log' => [
            'traceLevel' => getenv('APP_ENV') === 'prod' ? 0 : 3,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'maskVars' => ['_SERVER', '_COOKIE', '_POST'],
                    'logFile' => '@logs/api.log',
                ],
            ],
        ],
    ],
    'params'              => $params,
];
