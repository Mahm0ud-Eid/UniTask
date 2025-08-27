<?php

namespace common\models;

use common\enum\AttemptStatusEnum;
use common\enum\QuestionTypeEnum;
use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "quiz_attempt".
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $user_id
 * @property string|null $answers
 * @property int $grade
 * @property string $started_at
 * @property string|null $finished_at
 * @property string|null $ends_at
 * @property int $status
 *
 * @property Quizzes $quiz
 * @property User $user
 */
class QuizAttempt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quiz_attempt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quiz_id', 'user_id', 'started_at','ends_at'], 'required'],
            [['quiz_id', 'user_id', 'grade', 'status'], 'integer'],
            [['answers'], 'string'],
            [['started_at', 'finished_at', 'ends_at'], 'safe'],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quizzes::class, 'targetAttribute' => ['quiz_id' => 'id']],
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
            'quiz_id' => Yii::t('app', 'Quiz ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'answers' => Yii::t('app', 'Answers'),
            'grade' => Yii::t('app', 'Grade'),
            'started_at' => Yii::t('app', 'Started At'),
            'finished_at' => Yii::t('app', 'Finished At'),
            'ends_at' => Yii::t('app', 'Ends At'),
            'status' => Yii::t('app', 'Status'),
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
     * Gets query for [[Quiz]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz()
    {
        return $this->hasOne(Quizzes::class, ['id' => 'quiz_id']);
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

    public function isExpired(): bool
    {
        $endDate = date('Y-m-d H:i:s', strtotime($this->ends_at) + 60);
        if ($endDate < date('Y-m-d H:i:s') && $this->finished_at == null) {
            return true;
        }
        if ($this->finished_at != null) {
            return true;
        }
        return false;
    }

    public function reviewAnswers(): bool
    {
        $quiz = $this->quiz;
        $oldAnswers = json_decode($this->answers, true);
        $newAnswers = [];
        $grade = 0;
        $questions = $quiz->quizQuestions;
        $allQuestionsChecked = true;
        try {
            foreach ($questions as $question) {
                $question = $question->question;
                $questionId = $question->id;
                if ($question->question_type == QuestionTypeEnum::MCQ) {
                    $options = json_decode($question->options, true);
                    foreach ($options as $option) {
                        if ($option['option_text'] == $oldAnswers[$questionId]) {
                            $isCorrect = ($question->correct_answer == $option['id']);
                            $newAnswers[$questionId] = [
                                'answer' => $option['option_text'],
                                'correct' => $isCorrect,
                            ];
                            if ($isCorrect) {
                                $grade += $question->grade;
                            }
                        }
                    }
                } elseif ($question->question_type == QuestionTypeEnum::TRUE_FALSE) {
                    $isCorrect = ($question->correct_answer == $oldAnswers[$questionId]);
                    $newAnswers[$questionId] = [
                        'answer' => $oldAnswers[$questionId],
                        'correct' => $isCorrect,
                    ];
                    if ($isCorrect) {
                        $grade += $question->grade;
                    }
                } else {
                    if ($question->correct_answer != null) {
                        $isCorrect = ($question->correct_answer == $oldAnswers[$questionId]);
                        $newAnswers[$questionId] = [
                            'answer' => $oldAnswers[$questionId],
                            'correct' => $isCorrect,
                        ];
                        if ($isCorrect) {
                            $grade += $question->grade;
                        }
                    } else {
                        $newAnswers[$questionId] = [
                            'answer' => $oldAnswers[$questionId],
                            'correct' => false,
                        ];
                        $allQuestionsChecked = false;
                    }
                }
            }
            $this->grade = $grade;
            $this->answers = json_encode($newAnswers);
            $this->status = $allQuestionsChecked ? AttemptStatusEnum::REVIEWED : AttemptStatusEnum::NEEDS_REVIEW;
            $this->save();
        } catch (\Exception $e) {
            Yii::error('Error while reviewing answers for quiz ' . $quiz->id . ' ' . $e->getMessage());
            $this->status = AttemptStatusEnum::NEEDS_REVIEW;
            $this->save();
            return false;
        }
        return true;
    }
}
