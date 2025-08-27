<?php

use yii\db\Migration;

/**
 * Class m230324_113429_add_subject_quiz
 */
class m230324_113429_add_subject_quiz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('quizzes', 'subject_id', $this->integer()->notNull()->defaultValue(0));
        $this->addForeignKey('fk_quizzes_subject_id', 'quizzes', 'subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230324_113429_add_subject_quiz cannot be reverted.\n";

        return false;
    }
}
