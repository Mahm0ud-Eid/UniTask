<?php

use yii\db\Migration;

/**
 * Class m230308_080225_rename_cols
 */
class m230308_080225_rename_cols extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('departments', 'department_name', 'name');
        $this->renameColumn('years', 'year_name', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230308_080225_rename_cols cannot be reverted.\n";

        return false;
    }
}
