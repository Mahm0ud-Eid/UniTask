<?php

namespace frontend\modules\admin\models\teachers;

use common\models\Subjects;
use common\models\SubjectsAccess;
use common\models\User;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

/**
 * Class AddTeacherFormModel
 * @package frontend\models\user
 */
class AddTeacherFormModel extends Model
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
    public $subjects;

    /**
     * @var User
     */
    public $user;

    public function rules(): array
    {
        return [
            [['name', 'password', 'email', 'subjects'], 'required'],
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
            [['subjects'], 'each', 'rule' => ['integer']],
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
        $this->user->is_teacher = 1;
        if (!$this->user->save()) {
            Yii::error('Failed to save valid user: ' . json_encode($this->user->errors));
            return false;
        }
        // add auth item
        $auth = Yii::$app->authManager;
        $teachers = $auth->getRole('teachers');
        $auth->assign($teachers, $this->user->id);
        // add subjects to SubjectsAccess
        foreach ($this->subjects as $subject) {
            $subjectAccess = new SubjectsAccess();
            $subjectAccess->user_id = $this->user->id;
            $subjectAccess->subject_id = $subject;
            $subjectAccess->access_type = 1;
            if (!$subjectAccess->save()) {
                Yii::error('Failed to save valid subject access: ' . json_encode($subjectAccess->errors));
                return false;
            }
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
