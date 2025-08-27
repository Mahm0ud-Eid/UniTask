<?php

use notamedia\sentry\SentryTarget;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\helpers\Html;
use yii\log\FileTarget;
use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;
use yii\queue\LogBehavior;
use yii\rbac\DbManager;
use yii\symfonymailer\Mailer;
use yii\twig\ViewRenderer;
use yii\web\View;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
        'view' => [
            'class' => View::class,
            'renderers' => [
                'twig' => [
                    'class' => ViewRenderer::class,
                    'cachePath' => '@runtime/Twig/cache',
                    // Array of twig options:
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => [
                        'html' => ['class' => Html::class],
                    ],
                    'uses' => ['yii\bootstrap'],
                ],
            ],
        ],
        'mailer' => [
            'class' => Mailer::class,
            'transport' => [
                'scheme' => 'smtps',
                'host' => getenv('SMTP_HOST'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'port' => getenv('SMTP_PORT'),
                'options' => ['ssl' => true],
            ],
            'viewPath' => '@common/mail',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [getenv('SMTP_FROM') => getenv('SMTP_FROM_NAME')],
            ],
        ],
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
            'username' => getenv('MYSQL_USERNAME'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => getenv('APP_ENV') === 'prod',
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',

        ],
        'queue' => [
            'class' => Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'deleteReleased' => true,
            'mutex' => MysqlMutex::class,
            'ttr' => 600,
            'attempts' => 1,
            'as log' => LogBehavior::class,
        ],
        'queue2' => [
            'class' => Queue::class,
            'db' => 'db',
            'tableName' => 'queue2',
            'channel' => 'default',
            'deleteReleased' => true,
            'mutex' => MysqlMutex::class,
            'ttr' => 600,
            'attempts' => 1,
            'as log' => LogBehavior::class,
        ],
        'queue3' => [
            'class' => Queue::class,
            'db' => 'db',
            'tableName' => 'queue3',
            'channel' => 'default',
            'deleteReleased' => true,
            'mutex' => MysqlMutex::class,
            'ttr' => 600,
            'attempts' => 1,
            'as log' => LogBehavior::class,
        ],
        'queue4' => [
            'class' => Queue::class,
            'db' => 'db',
            'tableName' => 'queue4',
            'channel' => 'default',
            'deleteReleased' => true,
            'mutex' => MysqlMutex::class,
            'ttr' => 600,
            'attempts' => 1,
            'as log' => LogBehavior::class,
        ],
        'queue5' => [
            'class' => Queue::class,
            'db' => 'db',
            'tableName' => 'queue5',
            'channel' => 'default',
            'deleteReleased' => true,
            'mutex' => MysqlMutex::class,
            'ttr' => 600,
            'attempts' => 1,
            'as log' => LogBehavior::class,
        ],
        'log' => [
            'traceLevel' => getenv('APP_ENV') === 'prod' ? 0 : 3,
            'targets' => [
                [
                    'class' => SentryTarget::class,
                    'dsn' => getenv('SENTRY_DSN'),
                    'levels' => ['error'],

                    'context' => true,
                    'clientOptions' => [
//                        'environment' => getenv('APP_ENV'),
                        'release' => '0.2.1',//VERSION-HERE,
                        'traces_sample_rate' => 0.1,
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\i18n\*',
                        'yii\authclient\InvalidResponseException',
//                        'yii\queue\Queue',
//                        'yii\db\Exception',
                    ],
                ],
                [
                    'class' => FileTarget::class,
                    'enabled' => true,
                    'categories' => ['jobs'],
                    'levels' => ['info', 'warning', 'error'],
                    'maskVars' => ['_SERVER', '_COOKIE', '_POST'],
                    'logFile' => '@logs/jobs.log',
                ],
                [
                    'class' => FileTarget::class,
                    'enabled' => true,
                    'levels' => ['error'],
                    'maskVars' => ['_SERVER', '_COOKIE', '_POST'],
                    'logFile' => '@logs/global.log',
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \yii\widgets\LinkPager::class => \yii\bootstrap4\LinkPager::class,
            'yii\bootstrap5\LinkPager' => [
                'prevPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-left']),
                'nextPageLabel' => Html::tag('i', "", ['class' => 'fa fa-angle-right']),
                'firstPageLabel'=>Html::tag('i', "", ['class' => 'fa fa-angle-double-left']),
                'lastPageLabel'=>  Html::tag('i', "", ['class' => 'fa fa-angle-double-right']),
            ],
        ]
    ]
];
