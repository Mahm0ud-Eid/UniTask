<?php

use yii\db\Migration;

/**
 * Class m230307_154807_remake_tables
 */
class m230307_154807_remake_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%user}}');
        $tableOptions = 'ENGINE=InnoDB';
        $this->createTable(
            '{{%user}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'email' => $this->string(255)->notNull(),
                'password_hash' => $this->string(255)->notNull(),
                'auth_key' => $this->string(255)->notNull(),
                'last_login_at' => $this->datetime()->null()->defaultValue(null),
                'registration_ip' => $this->string(255)->notNull(),
                'api_key' => $this->string(255)->null()->defaultValue(null),
                'language' => $this->string(45)->null()->defaultValue('en-US'),
                'is_admin' => $this->integer()->notNull()->defaultValue(0),
                'is_teacher' => $this->integer()->notNull()->defaultValue(0),
            ],
            $tableOptions
        );
        $this->createTable(
            '{{%student_department_year}}',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'department' => $this->integer()->notNull()->defaultValue(0),
                'year' => $this->integer()->notNull()->defaultValue(0),
            ],
            $tableOptions
        );
        $this->createIndex('department', 'student_department_year', ['department'], false);
        $this->addForeignKey(
            'fk-student_department_year-department',
            'student_department_year',
            'department',
            'departments',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('year', 'student_department_year', ['year'], false);
        $this->addForeignKey(
            'fk-student_department_year-year',
            'student_department_year',
            'year',
            'years',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_154807_remake_tables cannot be reverted.\n";

        return false;
    }
}
