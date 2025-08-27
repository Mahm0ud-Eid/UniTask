<?php

use yii\db\Migration;

/**
 * Class m230308_073515_relations
 */
class m230308_073515_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-student_department_year-department', 'student_department_year');
        $this->dropForeignKey('fk-student_department_year-year', 'student_department_year');

        $this->createIndex('students_department_id', 'student_department_year', ['department_id'], false);
        $this->addForeignKey(
            'fk-student_department_year-department_id',
            'student_department_year',
            'department_id',
            'departments',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('students_year_id', 'student_department_year', ['year_id'], false);
        $this->addForeignKey(
            'fk-student_department_year-year_id',
            'student_department_year',
            'year_id',
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
        echo "m230308_073515_relations cannot be reverted.\n";

        return false;
    }
}
