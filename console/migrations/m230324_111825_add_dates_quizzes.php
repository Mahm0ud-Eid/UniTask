<?php

use yii\db\Migration;

/**
 * Class m230324_111825_add_dates_quizzes
 */
class m230324_111825_add_dates_quizzes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('quizzes', 'created_at', $this->dateTime()->null()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('quizzes', 'starts_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('quizzes', 'expires_at', $this->dateTime()->notNull()->defaultValue(null));
        $this->addColumn('quiz_attempt', 'started_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('quiz_attempt', 'finished_at', $this->dateTime()->notNull()->defaultValue(null));
        $this->addColumn('quiz_attempt', 'ends_at', $this->dateTime()->notNull()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230324_111825_add_dates_quizzes cannot be reverted.\n";

        return false;
    }
}
