<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "task_attempt".
 *
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property string $created_at
 * @property int|null $grade
 * @property string|null $student_comment
 * @property string|null $teacher_comment
 *
 * @property Tasks $task
 * @property TaskAttemptFiles[] $files
 * @property User $user
 */
class TaskAttempt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_attempt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'task_id'], 'required'],
            [['user_id', 'task_id', 'grade'], 'integer'],
            [['created_at'], 'safe'],
            [['student_comment', 'teacher_comment'], 'string'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
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
            'user_id' => Yii::t('app', 'User ID'),
            'task_id' => Yii::t('app', 'Task ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'grade' => Yii::t('app', 'Grade'),
            'student_comment' => Yii::t('app', 'Student Comment'),
            'teacher_comment' => Yii::t('app', 'Teacher Comment'),
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
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[TaskAttemptFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(TaskAttemptFiles::class, ['task_attempt_id' => 'id']);
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
}
