<?php

use yii\db\Migration;

/**
 * Class m230420_125657_make_file_null
 */
class m230420_125657_make_file_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('task_attempt', 'file_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('task_attempt', 'file_id', $this->integer()->notNull());
        return true;
    }
}
