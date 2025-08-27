<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "task_attempt_files".
 *
 * @property int $id
 * @property int $task_attempt_id
 * @property int $file_id
 *
 * @property Files $file
 * @property TaskAttempt $taskAttempt
 */
class TaskAttemptFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_attempt_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_attempt_id', 'file_id'], 'required'],
            [['task_attempt_id', 'file_id'], 'integer'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['task_attempt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAttempt::class, 'targetAttribute' => ['task_attempt_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task_attempt_id' => Yii::t('app', 'Task Attempt ID'),
            'file_id' => Yii::t('app', 'File ID'),
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
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[TaskAttempt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAttempt()
    {
        return $this->hasOne(TaskAttempt::class, ['id' => 'task_attempt_id']);
    }
}
