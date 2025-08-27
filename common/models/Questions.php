<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property int $id
 * @property int $user_id
 * @property string $question_text
 * @property string|null $question_type
 * @property string|null $options
 * @property string|null $correct_answer
 * @property int|null $grade
 * @property string|null $difficulty
 *
 * @property QuizQuestion[] $quizQuestions
 * @property User $user
 */
class Questions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'question_text'], 'required'],
            [['user_id', 'grade'], 'integer'],
            [['question_text', 'question_type', 'options', 'correct_answer', 'difficulty'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'question_text' => Yii::t('app', 'Question Text'),
            'question_type' => Yii::t('app', 'Question Type'),
            'options' => Yii::t('app', 'Options'),
            'correct_answer' => Yii::t('app', 'Correct Answer'),
            'grade' => Yii::t('app', 'Grade'),
            'difficulty' => Yii::t('app', 'Difficulty'),
        ];
    }

    /**
     * Gets query for [[QuizQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, ['question_id' => 'id']);
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
