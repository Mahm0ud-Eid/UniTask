<?php

namespace common\validators;

use Curl\Curl;
use Yii;

class HCaptchaValidator extends \yii\validators\Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (empty($model->$attribute)) {
            $this->addError($model, $attribute, "Captcha is required");
            \Yii::$app->session->setFlash('danger', 'Captcha is required');
            return;
        }
        $curl = new Curl();
        $curl->post('https://api.hcaptcha.com/siteverify', [
            'response' => $model->$attribute,
            'secret' => getenv('HCAPTCHA_PRIVATE_KEY'),
            'remoteip' => \Yii::$app->request->getUserIP(),
        ]);
        if ($curl->response->success !== true) {
            $this->addError($model, $attribute, "Captcha is invalid");
            \Yii::$app->session->setFlash('danger', 'Captcha is invalid');
        }
        parent::validateAttribute($model, $attribute);
    }

    protected function validateValue($value)
    {
        return null;
    }


}
