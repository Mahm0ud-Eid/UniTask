<?php

use yii\db\Migration;

/**
 * Class m230304_164728_years
 */
class m230304_164728_years extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%years}}',
            [
                'id' => $this->primaryKey(),
                'year' => $this->string(255)->notNull(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%years}}');
        return true;
    }
}
