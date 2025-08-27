<?php

namespace common\jobs;

use common\models\QuizAttempt;
use yii\queue\JobInterface;

/**
 * Class ReviewAnswersJob
 * @package common\jobs
 */

class ReviewAnswersJob implements JobInterface
{
    /**
     * @var QuizAttempt
     */
    public $quizAttempt;

    /**
     * ReviewAnswersJob constructor.
     * @param QuizAttempt $quizAttempt
     */
    public function __construct(QuizAttempt $quizAttempt)
    {
        $this->quizAttempt = $quizAttempt;
    }
    /**
     * @param \yii\queue\Queue $queue
     * @return mixed|void
     */
    public function execute($queue)
    {
        if ($this->quizAttempt->reviewAnswers()) {
            return true;
        }
        return false;
    }
}
