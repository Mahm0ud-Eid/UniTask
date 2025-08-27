<?php

namespace api\modules\v1;

use common\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\base\Action;
use yii\base\Module;
use yii\web\Response;

/**
 * v1 module definition class
 */

class APIv1 extends Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public static function getRoutes(): array
    {
        return [
            '/v1/add-students' => '/v1/user/add-students',
            '/v1/quiz/<id>/create-quiz' => '/v1/quiz/create-quiz',
            '/v1/quiz/<id>/update-quiz' => '/v1/quiz/update-quiz',
            '/v1/quiz/<id>/take-quiz' => '/v1/quiz/take-quiz',
            '/v1/result/<id>/mark-as-correct/<qid>' => '/v1/result/mark-as-correct',
            '/v1/result/<id>/mark-as-incorrect/<qid>' => '/v1/result/mark-as-incorrect',
            '/v1/result/<id>/mark-as-reviewed' => '/v1/result/mark-as-reviewed',
            '/v1/result/<id>/change-grade' => '/v1/result/change-grade',
        ];
    }
    /**
     * @param Action $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        Yii::$app->getResponse()->getHeaders()->set('Origin', '*');
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Request-Method', 'POST, GET');
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Headers', 'Authorization,DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Authorization');
        /** @phpstan-ignore-next-line */
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->method !== 'OPTIONS') {
            Yii::$app->session->remove('user_id');
            $api_key = Yii::$app->request->headers->get('Authorization');
            /** @noinspection TypeUnsafeComparisonInspection */
            if ($api_key === null) {
                Yii::$app->response->statusCode = 403;
                Yii::$app->response->content = json_encode(
                    [
                        'success' => false,
                        'error' => 'Missing Authorization header',
                    ]
                );
                return false;
            }
            $isScopedKey = str_starts_with($api_key, "SCOPED ");
            $scope = null;
            $scopeRestriction = null;
            if ($isScopedKey) {
                $jwt = str_replace("SCOPED ", "", $api_key);
                try {
                    $data = JWT::decode($jwt, new Key(getenv('JWT_SECRET'), 'HS256'));
                    $user = User::findOne($data->user_id);
                    if ($user === null || md5($user->api_key) !== $data->base_key) {
                        Yii::$app->response->statusCode = 403;
                        Yii::$app->response->content = json_encode(
                            [
                                'success' => false,
                                'error' => 'Invalid API Key',
                        ]
                        );
                        return false;
                    }
                    $scope = $data->scope;
                    $scopeRestriction = $data->scopeRestriction;
                } catch(\Exception $e) {
                    Yii::$app->response->statusCode = 403;
                    Yii::$app->response->content = json_encode(
                        [
                            'success' => false,
                            'error' => 'Invalid API Key',
                        ]
                    );
                    return false;
                }
                if (!$this->scopeAllowsAccess(Yii::$app->controller->id, Yii::$app->controller->action->id, $scope)) {
                    Yii::$app->response->statusCode = 403;
                    Yii::$app->response->content = json_encode(
                        [
                            'success' => false,
                            'error' => 'Restricted endpoint',
                        ]
                    );
                    return false;
                }
            } else {
                $user = User::findOne(['api_key' => $api_key]);
                if ($user === null) {
                    Yii::$app->response->statusCode = 403;
                    Yii::$app->response->content = json_encode(
                        [
                            'success' => false,
                            'error' => 'Invalid API Key',
                        ]
                    );
                    return false;
                }
            }
            Yii::$app->session->set('user_id', $user->id);
            Yii::$app->session->set('user_scope', $scope);
            Yii::$app->session->set('user_scope_restriction', $scopeRestriction);
            Yii::$app->user->login($user);
            if (getenv('MAINTENANCE_MODE') === 'on' && Yii::$app->user->identity->is_admin !== 1) {
                Yii::$app->response->content = json_encode(
                    [
                        'success' => false,
                        'error' => 'System under maintenance: (' . getenv('MAINTENANCE_MESSAGE') . ')',
                    ]
                );
                return false;
            }
        } else {
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->content = '{}';
            return false;
        }
        return parent::beforeAction($action);
    }

    private function scopeAllowsAccess(string $controller, string $action, string $scope)
    {
        if ($scope === 'GLOBAL') {
            return true;
        }
        $config = [
            'USER' => [
            ],
            'WEB' => [
                'user' => ['add-students'],
                'quiz' => ['create-quiz', 'update-quiz', 'take-quiz',],
                'result' => ['mark-as-correct', 'mark-as-incorrect', 'mark-as-reviewed', 'change-grade']
            ],
        ];
        if (!array_key_exists($scope, $config) || !array_key_exists($controller, $config[$scope]) || !in_array($action, $config[$scope][$controller])) {
            return false;
        }
        return true;
    }
    public function afterAction($action, $result)
    {
        if (Yii::$app->session->id) {
            Yii::$app->session->destroy();
        }
        return parent::afterAction($action, $result);
    }
}
