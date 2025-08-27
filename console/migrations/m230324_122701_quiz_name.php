<?php

use yii\db\Migration;

/**
 * Class m230324_122701_quiz_name
 */
class m230324_122701_quiz_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('quizzes', 'name', $this->string()->notNull()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230324_122701_quiz_name cannot be reverted.\n";

        return false;
    }
}
