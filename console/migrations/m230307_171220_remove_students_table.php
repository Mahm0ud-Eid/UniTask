<?php

use yii\db\Migration;

/**
 * Class m230307_171220_remove_students_table
 */
class m230307_171220_remove_students_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%students}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_171220_remove_students_table cannot be reverted.\n";

        return false;
    }
}
