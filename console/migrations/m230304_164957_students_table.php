<?php

use yii\db\Migration;

/**
 * Class m230304_164957_students_table
 */
class m230304_164957_students_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%students}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'email' => $this->string(255)->notNull(),
                'password_hash' => $this->string(255)->notNull(),
                'auth_key' => $this->string(255)->notNull(),
                'confirmed_at' => $this->datetime()->null()->defaultValue(null),
                'created_at' => $this->datetime()->null()->defaultExpression("CURRENT_TIMESTAMP"),
                'last_login_at' => $this->datetime()->null()->defaultValue(null),
                'year' => $this->integer()->notNull(),
                'department' => $this->integer()->notNull(),
                'registration_ip' => $this->string(255)->notNull(),
                'api_key' => $this->string(255)->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('department', 'students', ['department'], false);
        $this->addForeignKey(
            'fk-students-department',
            'students',
            'department',
            'departments',
            'id'
        );
        $this->createIndex('year', 'students', ['year'], false);
        $this->addForeignKey(
            'fk-students-year',
            'students',
            'year',
            'years',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_164957_students_table cannot be reverted.\n";

        return false;
    }
}
