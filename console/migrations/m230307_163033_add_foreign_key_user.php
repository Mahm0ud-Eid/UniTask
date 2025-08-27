<?php

use yii\db\Migration;

/**
 * Class m230307_163033_add_foreign_key_user
 */
class m230307_163033_add_foreign_key_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('user_id', 'student_department_year', ['user_id'], false);
        $this->addForeignKey(
            'fk-student_department_year-user_id',
            'student_department_year',
            'user_id',
            'user',
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
        echo "m230307_163033_add_foreign_key_user cannot be reverted.\n";

        return false;
    }
}
