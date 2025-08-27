<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $active
 * @property string|null $created_at
 * @property string $starts_at
 * @property string $ends_at
 * @property int $user_id
 * @property int $results_visibility
 * @property int $department_id
 * @property int $semester_id
 * @property int $subject_id
 * @property string|null $file_types
 *
 * @property Departments $department
 * @property Semesters $semester
 * @property Subjects $subject
 * @property TaskAttempt[] $taskAttempts
 * @property User $user
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'ends_at', 'user_id', 'results_visibility', 'department_id', 'semester_id', 'subject_id'], 'required'],
            [['description'], 'string'],
            [['active', 'user_id', 'results_visibility', 'department_id', 'semester_id', 'subject_id'], 'integer'],
            [['created_at', 'starts_at', 'ends_at'], 'safe'],
            [['title', 'file_types'], 'string', 'max' => 255],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['department_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semesters::class, 'targetAttribute' => ['semester_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'active' => Yii::t('app', 'Active'),
            'created_at' => Yii::t('app', 'Created At'),
            'starts_at' => Yii::t('app', 'Starts At'),
            'ends_at' => Yii::t('app', 'Ends At'),
            'user_id' => Yii::t('app', 'User ID'),
            'results_visibility' => Yii::t('app', 'Results Visibility'),
            'department_id' => Yii::t('app', 'Department ID'),
            'semester_id' => Yii::t('app', 'Semester ID'),
            'subject_id' => Yii::t('app', 'Subject ID'),
            'file_types' => Yii::t('app', 'File Types'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::class,
            ],
        ];
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['id' => 'department_id']);
    }

    /**
     * Gets query for [[Semester]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemester()
    {
        return $this->hasOne(Semesters::class, ['id' => 'semester_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subjects::class, ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[TaskAttempts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAttempts()
    {
        return $this->hasMany(TaskAttempt::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function isAvailable(): bool
    {
        if ($this->starts_at > date('Y-m-d H:i:s')) {
            return false;
        }
        if ($this->ends_at < date('Y-m-d H:i:s')) {
            return false;
        }
        if ($this->active == 0) {
            return false;
        }
        return true;
    }
}
