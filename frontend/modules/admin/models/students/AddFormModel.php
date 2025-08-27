<?php

namespace frontend\modules\admin\models\students;

use common\models\StudentDepartmentYear;
use common\models\User;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class AddFormModel
 * @package frontend\models\user
 */
class AddFormModel extends Model
{
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
     * @var
     */
    public $department_id;
    /**
     * @var
     */
    public $semester_id;


    /**
     * @var User
     */
    public $user;

    public function rules(): array
    {
        return [
            [['name', 'password', 'email', 'department_id', 'semester_id'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            [['email'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
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
            [['semester_id','department_id'], 'integer'],
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
        if (!$this->user->save()) {
            Yii::error('Failed to save valid user: ' . json_encode($this->user->errors));
            return false;
        }
        // add auth item
        $auth = Yii::$app->authManager;
        $student = $auth->getRole('students');
        $auth->assign($student, $this->user->id);

        // add department year
        $departmentYear = new StudentDepartmentYear();
        $departmentYear->user_id = $this->user->id;
        $departmentYear->department_id = $this->department_id;
        $departmentYear->semester_id = $this->semester_id;
        if (!$departmentYear->save()) {
            Yii::error('Failed to save valid department year: ' . json_encode($departmentYear->errors));
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
            if (strlen($this->password) < 8) {
                $this->addError('password', Yii::t('app', 'Password is too short'));
                return false;
            }
            return true;
        }
        return parent::beforeValidate();
    }
}
