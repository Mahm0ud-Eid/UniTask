<?php

namespace frontend\controllers;

use common\enum\PermissionType;
use common\enum\QuizVisibilityEnum;
use common\models\QuizAttempt;
use common\models\Quizzes;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Quizzes controller for student
 */
class QuizzesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'start', 'result-index', 'result-view'],
                        'allow' => true,
                        'roles' => [PermissionType::STUDENTS],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays index page for quizzes available to student.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $quizzes = Yii::$app->user->identity->getAvailableQuizzes();
        // check if attempted
        $listQuizzes = [];
        foreach ($quizzes as $quiz) {
            $quiz->expires_at = date('Y-m-d g:i:s A', strtotime($quiz->expires_at) + 7200);
            $attempt = QuizAttempt::find()->where(['quiz_id' => $quiz->id, 'user_id' => Yii::$app->user->id])->all();
            if (empty($attempt)) {
                $listQuizzes[] = $quiz;
            } else {
                foreach ($attempt as $att) {
                    if (!$att->isExpired()) {
                        $listQuizzes[] = $quiz;
                    }
                }
            }
        }
        return $this->render('index.twig', [
            'quizzes' => $listQuizzes,
        ]);
    }


    /**
     * Displays start page for quiz.
     *
     * @return mixed
     */
    public function actionStart($id)
    {
        $user = Yii::$app->user->identity;
        $now = date('Y-m-d H:i:s');
        $quiz = Quizzes::find()->where(['department_id' => $user->studentDepartmentYears->department_id,
            'semester_id' => $user->studentDepartmentYears->semester_id])
            ->andWhere(['<=', 'starts_at', $now])
            ->andWhere(['>=', 'expires_at', $now])
            ->andWhere(['active' => 1])
            ->andWhere(['id' => $id])
            ->one();
        if ($quiz) {
            $questions = [];
            foreach ($quiz->quizQuestions as $question) {
                $question->question->options = $question->question->options ?
                    json_decode($question->question->options, true) : [];
                $questions[] = $question->question;
            }
            $attempt = $quiz->startQuiz($user->id);
            if ($attempt !== false && $attempt->ends_at > $now) {
                return $this->render('start.twig', [
                    'questions' => $questions,
                    'attempt' => $attempt,
                ]);
            } else {
                Yii::$app->session->setFlash('error', 'Quiz is no longer available');
                return $this->redirect(['index']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Quiz not found');
            return $this->redirect(['index']);
        }
    }

    public function actionResultView($id)
    {
        $user = Yii::$app->user->identity;
        $attempt = QuizAttempt::find()->where(['id' => $id, 'user_id' => $user->id])->one();
        if ($attempt) {
            if ($attempt->quiz->results_visibility == QuizVisibilityEnum::NEVER) {
                Yii::$app->session->setFlash('error', 'Quiz results are not available');
                return $this->redirect(['result-index']);
            }
            if ($attempt->quiz->results_visibility == QuizVisibilityEnum::AFTER_QUIZ_TIME
                && $attempt->quiz->expires_at > date('Y-m-d H:i:s')) {
                Yii::$app->session->setFlash('error', "Quiz results are not available yet");
                return $this->redirect(['result-index']);
            }
            if ($attempt->quiz->results_visibility == QuizVisibilityEnum::AFTER_QUIZ or
                $attempt->quiz->results_visibility == QuizVisibilityEnum::AFTER_QUIZ_TIME) {
                $quiz = $attempt->quiz;
                $questions = [];
                $attempt->answers = $attempt->answers ? json_decode($attempt->answers, true) : [];
                foreach ($quiz->quizQuestions as $question) {
                    $question->question->options = $question->question->options ?
                        json_decode($question->question->options, true) : [];
                    $questions[] = $question->question;
                }
                return $this->render('result.twig', [
                    'questions' => $questions,
                    'attempt' => $attempt,
                ]);
            }
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Quiz not found');
            return $this->redirect(['result-index']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function actionResultIndex()
    {
        $user = Yii::$app->user->identity;
        $now = date('Y-m-d H:i:s');
        $quizzes = QuizAttempt::findAll(['user_id' => $user->id]);
        return $this->render('result-index.twig', [
            'attempts' => $quizzes,
            'now' => $now,
        ]);
    }


    public static function getRoutes(): array
    {
        return [
            'quizzes' => 'quizzes/index',
            'quizzes/<id>/start' => 'quizzes/start',

            'quizzes/results' => 'quizzes/result-index',
            'quizzes/<id>/result' => 'quizzes/result-view',
        ];
    }
}
