<?php

use yii\db\Migration;

/**
 * Class m230319_150832_rename_years_to_semsters
 */
class m230319_150832_rename_years_to_semsters extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('years', 'semesters');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('semesters', 'years');
        return true;
    }
}
