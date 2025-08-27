<?php

use yii\db\Migration;

/**
 * Class m230307_181155_rename_cols
 */
class m230307_181155_rename_cols extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('student_department_year', 'department', 'department_id');
        $this->renameColumn('student_department_year', 'year', 'year_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_181155_rename_cols cannot be reverted.\n";

        return false;
    }
}
