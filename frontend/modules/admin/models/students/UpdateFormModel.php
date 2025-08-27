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
class UpdateFormModel extends Model
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
            [['name', 'email', 'department_id', 'semester_id'], 'required'],
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
            [['semester_id','department_id'], 'integer'],
        ];
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $this->user = User::findOne($this->id);
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        if ($this->password !== '') {
            $this->user->setPassword($this->password);
        }
        if (!$this->user->save()) {
            Yii::error('Failed to save valid user: ' . json_encode($this->user->errors));
            return false;
        }

        $studentDepartmentYear = StudentDepartmentYear::findOne(['user_id' => $this->user->id]);
        $studentDepartmentYear->department_id = $this->department_id;
        $studentDepartmentYear->semester_id = $this->semester_id;
        $studentDepartmentYear->save();
        return true;
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            if (strlen($this->password) < 8 && $this->password !== '') {
                $this->addError('password', Yii::t('app', 'Password is too short'));
                return false;
            }
            return true;
        }
        return parent::beforeValidate();
    }
}
