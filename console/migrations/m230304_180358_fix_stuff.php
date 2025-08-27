<?php

use yii\db\Migration;

/**
 * Class m230304_180358_fix_stuff
 */
class m230304_180358_fix_stuff extends Migration
{
    public function up()
    {
        $this->renameColumn('years', 'year', 'year_name');
    }

    public function down()
    {
        $this->renameColumn('years', 'year', 'year_name');
    }
}
