<?php

namespace frontend\modules\admin\models\admins;

use common\models\User;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

/**
 * Class AdminFormModel
 * @package frontend\models\user
 */
class AdminFormModel extends Model
{
    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $password;
    /**
     * @var
     */
    public $email;

    /**
     * @var User
     */
    public $user;

    public function rules(): array
    {
        return [
            [['name', 'password', 'email'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email', 'filter' => function ($query) {
                $query->andWhere(['<>', 'id', $this->id]);
            }],
            [['email'], 'email'],
            [['password'], StrengthValidator::class,
                'min' => 8,
                'digit' => 1,
                'special' => 1,
                'lower' => 1,
                'upper' => 1,
                'userAttribute' => 'name',
                'preset' => 'normal',
            ],
        ];
    }

    /**
     * @return bool
     */
    public function add(): bool
    {
        $this->user = new User();
        $this->user->email = $this->email;
        $this->user->name = $this->name;
        $this->user->setPassword($this->password);
        $this->user->generateAuthKey();
        $this->user->registration_ip = Yii::$app->request->userIP;
        $this->user->generateApiKey();
        $this->user->is_admin = 1;
        if (!$this->user->save()) {
            Yii::error('Failed to save valid user: ' . json_encode($this->user->errors));
            return false;
        }
        // add auth item
        $auth = Yii::$app->authManager;
        $admins = $auth->getRole('admin');
        $auth->assign($admins, $this->user->id);
        // add subjects to SubjectsAccess
        return true;
    }


    /**
     * @return bool
     */
    public function update(): bool
    {
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        if ($this->password !== '') {
            $this->user->setPassword($this->password);
        }
        if (!$this->user->save()) {
            Yii::error('Failed to save valid user: ' . json_encode($this->user->errors));
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->email = strtolower($this->email);
            if (User::findOne(['email' => $this->email]) !== null) {
                $this->addError('email', Yii::t('app', 'This email is already in use'));
                return false;
            }
            return true;
        }
        return parent::beforeValidate();
    }

    /**
     * @param User $user
     */
    public function loadUser(User $user): void
    {
        $this->user = $user;
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
    }
}
