<?php

namespace frontend\modules\teacher\controllers;

use common\enum\AttemptStatusEnum;
use common\enum\PermissionType;
use common\jobs\ReviewAnswersJob;
use common\models\QuizAttempt;
use common\models\Quizzes;
use common\models\StudentDepartmentYear;
use common\models\Subjects;
use common\models\SubjectsAccess;
use common\models\User;
use common\utils\QueueUtils;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * QuizzesController implements the CRUD actions for Subjects model
 * for teachers where they can manage their subjects that they have access to using SubjectsAccess model
 */
class QuizzesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'view',
                            'create',
                            'update',
                            'activate',
                            'deactivate',
                        ],
                        'roles' => [PermissionType::MANAGE_SUBJECTS],
                        'roleParams' => ['subjectId' => Yii::$app->request->get('id')],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'results',
                            'delete-result',
                            'student-result',
                            'auto-review',
                        ],
                        'roles' => [PermissionType::CREATE_QUIZZES],
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Subjects models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $subjects = Subjects::find()->joinWith('subjectsAccesses')
            ->where(['subjects_access.user_id' => Yii::$app->user->id])->all();
        return $this->render('index.twig', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Displays a single Subjects model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $quizzes = Quizzes::find()->where(['subject_id' => $id])->all();
        $quizzes_count = [];
        foreach ($quizzes as $quiz) {
            // add amount of students and amount of students that have taken the quiz
            $quizzes_count[$quiz->id] = StudentDepartmentYear::find()->where(['department_id' => $quiz->department_id,
                'semester_id' => $quiz->semester_id])->count();
            $quizzes_count[$quiz->id] = [
                'total' => $quizzes_count[$quiz->id],
                // taken count is the count of attempts that have status 5 (completed) or 6 (reviewed)
                'taken' => $quiz->quizAttempts ? QuizAttempt::find()
                    ->where(['in', 'status', [AttemptStatusEnum::NEEDS_REVIEW, AttemptStatusEnum::REVIEWED]])
                    ->andWhere(['quiz_id' => $quiz->id])
                    ->count() : 0,
            ];
        }
        return $this->render('view.twig', [
            'subject' => Subjects::find()->where(['id' => $id])->one(),
            'quizzes' => $quizzes,
            'quizzes_count' => $quizzes_count,
        ]);
    }

    /**
     * Load the create quiz form
     * @param int $id subject ID
     */
    public function actionCreate($id)
    {
        return $this->render('create.twig', [
            'new' => true,
            'subject' => Subjects::find()->where(['id' => $id])->one(),
        ]);
    }

    /**
     * Edit the quiz
     * @param int $quiz_id quiz ID
     */
    public function actionUpdate(int $quiz_id)
    {
        $quiz = Quizzes::findOne($quiz_id);
        $listQ= [];
        $questions = $quiz->quizQuestions;
        foreach ($questions as $question) {
            // decode options
            $options = $question->question->options ? json_decode($question->question->options, true) : [];
            $listQ[] = [
                'id' => $question->question->id,
                'question' => $question->question->question_text,
                'type' => $question->question->question_type,
                'options' => $options,
                'answer' => $question->question->correct_answer,
                'grade' => $question->question->grade,
            ];
        }
        return $this->render('create.twig', [
            'new' => false,
            'quiz' => $quiz,
            'questions' => $listQ,
        ]);
    }

    public function actionActivate($id, $quiz_id)
    {
        $quiz = Quizzes::findOne($quiz_id);
        $quiz->active = 1;
        $quiz->save();
        Yii::$app->session->setFlash('success', 'Quiz activated');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public function actionDeactivate($id, $quiz_id)
    {
        $quiz = Quizzes::findOne($quiz_id);
        $quiz->active = 0;
        $quiz->save();
        Yii::$app->session->setFlash('success', 'Quiz deactivated');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public function actionResults($id)
    {
        $quiz = Quizzes::findOne($id);
        $access = SubjectsAccess::findOne(['subject_id' => $quiz->subject_id, 'user_id' => Yii::$app->user->id]);
        if ($access === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $results = QuizAttempt::find()->where(['quiz_id' => $id])->all();
        $students = User::find()->joinWith('studentDepartmentYears')
            ->where(['student_department_year.department_id' => $quiz->department_id,
                'student_department_year.semester_id' => $quiz->semester_id])->all();
        // add the students that have not taken the quiz to results with status 0 (not taken)
        foreach ($students as $student) {
            $found = false;
            foreach ($results as $result) {
                if ($result->user_id === $student->id) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $results[] = new QuizAttempt([
                    'user_id' => $student->id,
                    'quiz_id' => $id,
                    'status' => AttemptStatusEnum::NOT_STARTED,
                    'grade' => 0,
                ]);
            }
        }
        return $this->render('results', [
            'quiz' => $quiz,
            'results' => $results,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function actionDeleteResult($id)
    {
        $result = QuizAttempt::find()->where(['id' => $id])->one();
        $access = SubjectsAccess::findOne(['subject_id' => $result->quiz->subject_id, 'user_id' => Yii::$app->user->id]);
        if ($access === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $result_id = $result->quiz_id;
        $result->delete();
        Yii::$app->session->setFlash('success', 'Result deleted');
        return $this->redirect(['results', 'id' => $result_id]);
    }

    /**
     * @inheritDoc
     */
    public function actionStudentResult($id)
    {
        $result = QuizAttempt::find()->where(['id' => $id])->one();
        $quiz = $result->quiz;
        $access = SubjectsAccess::findOne(['subject_id' => $quiz->subject_id, 'user_id' => Yii::$app->user->id]);
        if ($access === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $result->answers = $result->answers ? json_decode($result->answers, true) : [];
        $total = 0;
        foreach ($quiz->quizQuestions as $question) {
            $question->question->options = $question->question->options ? json_decode($question->question->options, true) : [];
            $total += $question->question->grade;
        }
        return $this->render('student-result.twig', [
            'quiz' => $quiz,
            'result' => $result,
            'quizTotal' => $total,
        ]);
    }

    public function actionAutoReview($id)
    {
        $attempt = QuizAttempt::findOne(['id' => $id]);
        $access = SubjectsAccess::findOne(['subject_id' => $attempt->quiz->subject_id, 'user_id' => Yii::$app->user->id]);
        if ($access === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        if ($attempt->status !== AttemptStatusEnum::NEEDS_REVIEW && $attempt->status !== AttemptStatusEnum::REVIEWED) {
            Yii::$app->session->setFlash('error', 'This attempt cannot be auto-reviewed at this time');
            return $this->redirect(['student-result',
                'id' => $id
            ]);
        }
        $decodedAnswers = json_decode($attempt->answers, true);
        foreach ($decodedAnswers as $key => $answer) {
            if (is_array($answer)) {
                $decodedAnswers[$key] = $answer['answer'];
            }
        }
        $attempt->answers = json_encode($decodedAnswers);
        $attempt->status = AttemptStatusEnum::UNDER_REVIEW;
        $attempt->save();
        QueueUtils::getQueueLine()->push(new ReviewAnswersJob($attempt));
        Yii::$app->session->setFlash('success', 'Answers are being reviewed');
        return $this->redirect(['student-result',
            'id' => $id
        ]);
    }

    public static function getRoutes(): array
    {
        return [
            'teacher/quizzes' => 'teacher/quizzes/index',
            'teacher/quizzes/<id>' => 'teacher/quizzes/view',
            'teacher/quizzes/<id>/update/<quiz_id>' => 'teacher/quizzes/update',
            'teacher/quizzes/<id>/create' => 'teacher/quizzes/create',
            'teacher/quizzes/<id>/activate/<quiz_id>' => 'teacher/quizzes/activate',
            'teacher/quizzes/<id>/deactivate/<quiz_id>' => 'teacher/quizzes/deactivate',

            'teacher/quizzes/<id>/results' => 'teacher/quizzes/results',
            'teacher/quizzes/<id>/delete-result' => 'teacher/quizzes/delete-result',
            'teacher/quizzes/<id>/student-result' => 'teacher/quizzes/student-result',
            'teacher/quizzes/<id>/auto-review' => 'teacher/quizzes/auto-review',
        ];
    }
}
