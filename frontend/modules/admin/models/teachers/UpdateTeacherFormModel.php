<?php

namespace frontend\modules\admin\models\teachers;

use common\models\Subjects;
use common\models\SubjectsAccess;
use common\models\User;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

/**
 * Class UpdateTeacherFormModel
 * @package frontend\models\user
 */
class UpdateTeacherFormModel extends Model
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
    public $subjects;

    /**
     * @var User
     */
    public $user;

    public function rules(): array
    {
        return [
            [['name', 'email', 'subjects'], 'required'],
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
            [['subjects'], 'each', 'rule' => ['integer']],
        ];
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
        // remove subjects
        $existingSubjectAccesses = SubjectsAccess::findAll(['user_id' => $this->user->id]);
        $existingSubjectIds = array_map(function ($sa) {
            return $sa->subject_id;
        }, $existingSubjectAccesses);

        // delete the subject accesses that are not in the selected subjects list
        $deleteSubjectAccesses = array_diff($existingSubjectIds, $this->subjects);
        if (!empty($deleteSubjectAccesses)) {
            if (!SubjectsAccess::deleteAll(['user_id' => $this->user->id, 'subject_id' => $deleteSubjectAccesses])) {
                Yii::error('Failed to delete subject accesses.');
                return false;
            }
        }

        // create the new subject accesses
        $newSubjectIds = array_diff($this->subjects, $existingSubjectIds);
        foreach ($newSubjectIds as $subject) {
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
     * @param User $user
     */
    public function loadUser(User $user): void
    {
        $this->user = $user;
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->subjects = array_map(function ($sa) {
            return $sa->subject_id;
        }, $user->subjectsAccesses);
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
