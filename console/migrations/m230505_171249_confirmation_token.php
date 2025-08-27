<?php

use yii\db\Migration;

/**
 * Class m230505_171249_confirmation_token
 */
class m230505_171249_confirmation_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%confirmation_token}}',
            [
                'id' => $this->primaryKey(),
                'token_type' => $this->integer()->notNull(),
                'user_id' => $this->integer()->notNull(),
                'created_at' => $this->datetime()->null()->defaultExpression("CURRENT_TIMESTAMP"),
                'token' => $this->string(255)->notNull(),
            ],
            $tableOptions
        );
        $this->createIndex('fk_confirmation_token_1_idx', '{{%confirmation_token}}', ['user_id'], false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('fk_confirmation_token_1_idx', '{{%confirmation_token}}');
        $this->dropTable('{{%confirmation_token}}');

        return true;
    }
}
