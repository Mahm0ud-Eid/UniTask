<?php

use yii\db\Migration;

/**
 * Class m230304_164115_departments_table
 */
class m230304_164115_departments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%departments}}',
            [
                'id' => $this->primaryKey(),
                'department_name' => $this->string(255)->notNull(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%departments}}');
        return true;
    }
}
