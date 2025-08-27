<?php

use yii\db\Migration;

/**
 * Class m230505_165846_session
 */
class m230505_165846_session extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%session}}', [
            'id' => $this->char(64)->notNull(),
            'expire' => $this->integer(),
            'data' => $this->binary()
        ]);
        $this->addPrimaryKey('pk-id', '{{%session}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%session}}');

        return true;
    }
}
