<?php

namespace common\models;

use common\enum\AttemptStatusEnum;
use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "quizzes".
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property string|null $description
 * @property int $active
 * @property int $duration
 * @property int $results_visibility
 * @property int $department_id
 * @property int $semester_id
 * @property string|null $created_at
 * @property string $starts_at
 * @property string|null $expires_at
 * @property int $subject_id
 *
 * @property Departments $department
 * @property QuizAttempt[] $quizAttempts
 * @property QuizQuestion[] $quizQuestions
 * @property Semesters $semester
 * @property Subjects $subject
 * @property User $user
 *
 * @property int $totalGrade
 */
class Quizzes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quizzes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'user_id', 'active', 'duration', 'results_visibility', 'department_id', 'semester_id'], 'required'],
            [['user_id', 'active', 'duration', 'results_visibility', 'department_id', 'semester_id', 'subject_id'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'starts_at', 'expires_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['department_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semesters::class, 'targetAttribute' => ['semester_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::class,
                'excludedAttributes' => ['created_at', 'starts_at'],
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
            'name' => Yii::t('app', 'Name'),
            'user_id' => Yii::t('app', 'User ID'),
            'description' => Yii::t('app', 'Description'),
            'active' => Yii::t('app', 'Active'),
            'duration' => Yii::t('app', 'Duration'),
            'results_visibility' => Yii::t('app', 'Results Visibility'),
            'department_id' => Yii::t('app', 'Department ID'),
            'semester_id' => Yii::t('app', 'Semester ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'starts_at' => Yii::t('app', 'Starts At'),
            'expires_at' => Yii::t('app', 'Expires At'),
            'subject_id' => Yii::t('app', 'Subject ID'),
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
     * Gets query for [[QuizAttempts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, ['quiz_id' => 'id']);
    }

    /**
     * Gets query for [[QuizQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, ['quiz_id' => 'id']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function startQuiz($user_id)
    {
        $quizattempt = QuizAttempt::find()->where(['quiz_id' => $this->id, 'user_id' => $user_id])->one();
        if ($quizattempt) {
            return $quizattempt;
        }
        $attempt = new QuizAttempt();
        $attempt->quiz_id = $this->id;
        $attempt->user_id = $user_id;
        $attempt->started_at = date('Y-m-d H:i:s');
        $attempt->ends_at = date('Y-m-d H:i:s', strtotime('+' . $this->duration . ' minutes'));
        $attempt->grade = 0;
        $attempt->status = AttemptStatusEnum::IN_PROGRESS;
        if ($attempt->save()) {
            return $attempt;
        }
        return false;
    }

    public function isValid(): bool
    {
        if ($this->active == 0) {
            return false;
        }
        if ($this->starts_at > date('Y-m-d H:i:s')) {
            return false;
        }
        if ($this->expires_at < date('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function getQuestion($qid)
    {
        return QuizQuestion::find()->where(['quiz_id' => $this->id, 'question_id' => $qid])->one();
    }

    /**
     * @return int Total grade of the quiz
     */
    public function getTotalGrade(): int
    {
        $total = 0;
        foreach ($this->quizQuestions as $question) {
            $total += $question->question->grade;
        }
        return $total;
    }
}
