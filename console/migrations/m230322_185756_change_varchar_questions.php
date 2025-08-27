<?php

use yii\db\Migration;

/**
 * Class m230322_185756_change_varchar_questions
 */
class m230322_185756_change_varchar_questions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('questions', 'question_text', $this->text());
        $this->alterColumn('questions', 'options', $this->text());
        $this->alterColumn('quizzes', 'description', $this->text());
        $this->alterColumn('quiz_attempt', 'answers', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230322_185756_change_varchar_questions cannot be reverted.\n";

        return false;
    }
}
