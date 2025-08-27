<?php

namespace common\models;

use common\validators\HCaptchaValidator;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public const SCENARIO_UI = "userLoginUI";
    public $email;
    public $password;
    public $rememberMe = true;
    public $hcaptcha;
    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            [['hcaptcha'], HCaptchaValidator::class, 'on' => self::SCENARIO_UI, 'skipOnEmpty' => false],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate() && ($user = $this->getUser()) instanceof User) {
            $user->last_login_at = date('Y-m-d H:i:s');
            if ($user->registration_ip == null) {
                $user->registration_ip = Yii::$app->request->userIP;
            }
            // uncomment this if you want to regenerate auth_key on each login
            //            $user->auth_key = Yii::$app->security->generateRandomString(255);
            $user->save();
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
    public function load($data, $formName = null)
    {
        $val = parent::load($data, $formName);
        $this->hcaptcha = $data['h-captcha-response'] ?? null;
        return $val;
    }
}
