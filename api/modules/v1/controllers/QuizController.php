<?php

namespace api\modules\v1\controllers;

use common\enum\PermissionType;
use common\enum\QuestionTypeEnum;
use common\enum\QuizVisibilityEnum;
use common\jobs\ReviewAnswersJob;
use common\models\Questions;
use common\models\QuizAttempt;
use common\models\QuizQuestion;
use common\models\Quizzes;
use common\models\Subjects;
use common\models\SubjectsAccess;
use common\models\User;
use common\utils\QueueUtils;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\web\Controller;
use yii\web\Response;

class QuizController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/vnd.api+json' => Response::FORMAT_JSON,
                    ],
                ],
            ]
        );
    }

    public function actionCreateQuiz($id)
    {
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $id]);
        if ($subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $payload = json_decode(Yii::$app->request->rawBody, true);
        if (empty($payload) || json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return [
                'success' => false,
                'error' => 'Invalid payload',
            ];
        }
        $quiz = new Quizzes();
        $quiz->name = $payload['name'];
        $quiz->user_id = Yii::$app->session->get('user_id');
        $quiz->description = $payload['description'];
        $quiz->starts_at = !empty($payload['startTime']) ? $payload['startTime'] : date('Y-m-d H:i:s');

        $quiz->expires_at = !empty($payload['endTime']) ? $payload['endTime']
            : date('Y-m-d H:i:s', strtotime('+1 week'));
        $quiz->duration = $payload['time'] ?? 10;
        $quiz->results_visibility = !empty($payload['visibility']) ? $payload['visibility'] : QuizVisibilityEnum::NEVER;
        $quiz->active = !empty($payload['active']) ? $payload['active'] : 0;
        $quiz->subject_id = $id;
        $subject = Subjects::findOne(['id' => $id]);
        $quiz->department_id = $subject->department_id;
        $quiz->semester_id = $subject->semester_id;
        if (!$quiz->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save quiz',
            ];
        }
        $questions = $payload['questions'];
        foreach ($questions as $question) {
            $questionModel = new Questions();
            $questionModel->user_id = Yii::$app->session->get('user_id');
            $questionModel->question_text = $question['question_text'];
            $questionModel->question_type = $question['type'];
            $questionModel->grade = $question['grade'];
            if ($question['type'] == QuestionTypeEnum::MCQ) {
                $options = [];
                $correctAnswerId = null;
                foreach ($question['options'] as $option) {
                    // add id to every option and save it as json then save it in the db in options
                    $option['id'] = strval(count($options) + 1);
                    if ($option['isCorrect']) {
                        $correctAnswerId = $option['id'];
                    }
                    unset($option['isCorrect']);
                    $options[] = $option;
                }
                $questionModel->options = json_encode($options);
                $questionModel->correct_answer = $correctAnswerId;
            }
            if (!$questionModel->save()) {
                return [
                    'success' => false,
                    'error' => 'Failed to save question',
                ];
            }
            $quizQuestion = new QuizQuestion();
            $quizQuestion->quiz_id = $quiz->id;
            $quizQuestion->question_id = $questionModel->id;
            if (!$quizQuestion->save()) {
                return [
                    'success' => false,
                    'error' => 'Failed to save quiz question',
                ];
            }
        }
        return [
            'success' => true,
        ];
    }

    public function actionUpdateQuiz($id)
    {
        $quiz = Quizzes::findOne(['id' => $id]);
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $quiz->subject_id]);
        if ($quiz === null || $subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $payload = json_decode(Yii::$app->request->rawBody, true);
        if (empty($payload) || json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return [
                'success' => false,
                'error' => 'Invalid payload',
            ];
        }
        $quiz->name = $payload['name'];
        $quiz->description = $payload['description'];
        $quiz->starts_at = $payload['startTime'];
        $quiz->expires_at = $payload['endTime'];
        $quiz->duration = $payload['time'];
        $quiz->results_visibility = $payload['visibility'];
        $quiz->active = $payload['active'];
        if (!$quiz->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save quiz',
            ];
        }
        $questions = $payload['questions'];
        $questionsArray = [];
        foreach ($questions as $question) {
            $questionModel = null;
            if (!empty($question['id'])) {
                $questionModel = Questions::findOne(['id' => $question['id']]);
                $questionsArray[] = $question['id'];
            }
            if ($questionModel === null) {
                $questionModel = new Questions();
                $questionModel->user_id = Yii::$app->session->get('user_id');
            }
            $questionModel->question_text = $question['question_text'];
            $questionModel->question_type = $question['type'];
            $questionModel->grade = $question['grade'];
            if ($question['type'] == QuestionTypeEnum::MCQ) {
                $options = [];
                $correctAnswerId = null;
                foreach ($question['options'] as $option) {
                    $option['id'] = strval(count($options) + 1);
                    if ($option['isCorrect']) {
                        $correctAnswerId = $option['id'];
                    }
                    unset($option['isCorrect']);
                    $options[] = $option;
                }
                $questionModel->options = json_encode($options);
                $questionModel->correct_answer = $correctAnswerId;
            } elseif ($question['type'] == QuestionTypeEnum::TRUE_FALSE) {
                $questionModel->correct_answer = $question['correct_answer'];
            }
            if (!$questionModel->save()) {
                return [
                    'success' => false,
                    'error' => 'Failed to save question',
                ];
            }
            $questionsArray[] = $questionModel->id;
            $quizQuestion = QuizQuestion::findOne(['quiz_id' => $quiz->id, 'question_id' => $questionModel->id]);
            if ($quizQuestion === null) {
                $quizQuestion = new QuizQuestion();
                $quizQuestion->quiz_id = $quiz->id;
                $quizQuestion->question_id = $questionModel->id;
                if (!$quizQuestion->save()) {
                    return [
                        'success' => false,
                        'error' => 'Failed to save quiz question',
                    ];
                }
            }
        }
        QuizQuestion::deleteAll(['and', ['not in', 'question_id', $questionsArray], ['quiz_id' => $quiz->id]]);
        return [
            'success' => true,
        ];
    }

    public function actionTakeQuiz($id)
    {
        $quiz = Quizzes::findOne(['id' => $id]);
        if (!$quiz->isValid()) {
            return [
                'success' => false,
                'error' => 'Quiz is not valid',
            ];
        }
        $user = User::findOne(['id' => Yii::$app->session->get('user_id')]);
        if (($quiz->department->id !== $user->studentDepartmentYears->department->id)
        || ($quiz->semester->id !== $user->studentDepartmentYears->semester->id)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $attempt = QuizAttempt::findOne(['quiz_id' => $id, 'user_id' => Yii::$app->session->get('user_id')]);
        if ($attempt === null || $attempt->isExpired()) {
            return [
                'success' => false,
                'error' => 'Quiz not available',
            ];
        }
        $payload = json_decode(Yii::$app->request->rawBody, true);
        if (empty($payload) || json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return [
                'success' => false,
                'error' => 'Invalid payload',
            ];
        }
        foreach ($payload['answers'] as $questionId => $answer) {
            if (!is_string($answer)) {
                return [
                    'success' => false,
                    'error' => 'Invalid answer format',
                ];
            }
            // Sanitize answer
            $answer = HtmlPurifier::process($answer);
            $payload[$questionId] = $answer;
        }
        $attempt->answers = json_encode($payload['answers']);
        $attempt->finished_at = date('Y-m-d H:i:s');
        if (!$attempt->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save attempt',
            ];
        }
        $attempt = QuizAttempt::findOne(['id' => $attempt->id]);
        QueueUtils::getQueueLine()->push(new ReviewAnswersJob($attempt));
        return [
            'success' => true,
        ];
    }
}
