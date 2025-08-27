<?php

namespace api\modules\v1\controllers;

use common\enum\AttemptStatusEnum;
use common\enum\PermissionType;
use common\models\QuizAttempt;
use common\models\SubjectsAccess;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class ResultController extends Controller
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


    public function actionMarkAsCorrect($id, $qid)
    {
        $quizAttempt = QuizAttempt::findOne(['id' => $id]);
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $quizAttempt->quiz->subject_id]);
        if ($quizAttempt === null || $subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $quizAttempt->answers = json_decode($quizAttempt->answers, true);
        $updatedAnswers = $quizAttempt->answers;
        foreach ($updatedAnswers as $key => $answer) {
            if ($key == $qid) {
                $updatedAnswers[$key]['correct'] = true;
            }
        }
        $quizAttempt->answers = json_encode($updatedAnswers);
        $grade = $quizAttempt->grade;
        $payload = json_decode(Yii::$app->request->rawBody, true);
        if ($payload['grade'] !== null) {
            $grade += (int)$payload['grade'];
        } else {
            $grade += $quizAttempt->quiz->getQuestion($qid)->question->grade;
        }
        // make sure grade is not more than quiz grade using $quiz->getTotalGrade()
        if ($grade > $quizAttempt->quiz->getTotalGrade()) {
            $grade = $quizAttempt->quiz->getTotalGrade();
        }
        $quizAttempt->grade = $grade;
        if (!$quizAttempt->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save',
            ];
        }
        return [
            'success' => true,
            'grade' => $grade,
        ];
    }

    public function actionMarkAsIncorrect($id, $qid)
    {
        $quizAttempt = QuizAttempt::findOne(['id' => $id]);
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $quizAttempt->quiz->subject_id]);
        if ($quizAttempt === null || $subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $quizAttempt->answers = json_decode($quizAttempt->answers, true);
        $updatedAnswers = $quizAttempt->answers;
        foreach ($updatedAnswers as $key => $answer) {
            if ($key == $qid) {
                $updatedAnswers[$key]['correct'] = false;
            }
        }
        $quizAttempt->answers = json_encode($updatedAnswers);
        $questionGrade = $quizAttempt->quiz->getQuestion($qid)->question->grade;
        $grade = $quizAttempt->grade;
        $grade -= $questionGrade;
        if ($grade < 0) {
            $grade = 0;
        }
        $quizAttempt->grade = $grade;
        if (!$quizAttempt->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save',
            ];
        }
        return [
            'success' => true,
        ];
    }

    public function actionMarkAsReviewed($id)
    {
        $quizAttempt = QuizAttempt::findOne(['id' => $id]);
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $quizAttempt->quiz->subject_id]);
        if ($quizAttempt === null || $subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        if ($quizAttempt->status !== AttemptStatusEnum::NEEDS_REVIEW) {
            return [
                'success' => false,
                'error' => 'Attempt is not in need of review',
            ];
        }
        $quizAttempt->status = AttemptStatusEnum::REVIEWED;
        if (!$quizAttempt->save()) {
            return [
                'success' => false,
                'error' => 'Failed to save',
            ];
        }
        return [
            'success' => true,
        ];
    }

    public function actionChangeGrade($id)
    {
        $quizAttempt = QuizAttempt::findOne(['id' => $id]);
        $subjectAccess = SubjectsAccess::findOne(['user_id' => Yii::$app->session->get('user_id'), 'subject_id' => $quizAttempt->quiz->subject_id]);
        if ($quizAttempt === null || $subjectAccess === null || !Yii::$app->authManager->checkAccess(Yii::$app->session->get('user_id'), PermissionType::CREATE_QUIZZES)) {
            return [
                'success' => false,
                'error' => 'Permission denied',
            ];
        }
        $payload = json_decode(Yii::$app->request->rawBody, true);
        $quizAttempt->grade = $payload['grade'];
        if (!$quizAttempt->save()) {
            return [
                'success' => false,
                'error' => 'Failed to change grade',
            ];
        }
        return [
            'success' => true,
        ];
    }
}
