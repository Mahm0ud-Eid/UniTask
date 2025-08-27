<?php

use yii\db\Migration;

/**
 * Class m230319_151330_rename_quizzes_fields
 */
class m230319_151330_rename_quizzes_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // rename department to department_id
        $this->renameColumn('quizzes', 'department', 'department_id');
        // rename semester to semester_id
        $this->renameColumn('quizzes', 'semester', 'semester_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // rename department_id to department
        $this->renameColumn('quizzes', 'department_id', 'department');
        // rename semester_id to semester
        $this->renameColumn('quizzes', 'semester_id', 'semester');

        return true;
    }
}
