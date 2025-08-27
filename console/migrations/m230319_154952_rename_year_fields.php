<?php

use yii\db\Migration;

/**
 * Class m230319_154952_rename_year_fields
 */
class m230319_154952_rename_year_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('student_department_year', 'year_id', 'semester_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('student_department_year', 'semester_id', 'year_id');

        return false;
    }
}
