<?php

use yii\db\Migration;

/**
 * Class m230420_115511_add_fields
 */
class m230420_115511_add_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tasks', 'starts_at', $this->dateTime()
            ->notNull()->after('created_at')->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('task_attempt', 'created_at', $this->datetime()
            ->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230420_115511_add_fields cannot be reverted.\n";

        return false;
    }
}
