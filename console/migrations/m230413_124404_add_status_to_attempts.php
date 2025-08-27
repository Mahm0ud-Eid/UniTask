<?php

use yii\db\Migration;

/**
 * Class m230413_124404_add_status_to_attempts
 */
class m230413_124404_add_status_to_attempts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('quiz_attempt', 'status', $this->integer()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('quiz_attempt', 'status');
        return true;
    }
}
