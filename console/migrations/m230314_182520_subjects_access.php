<?php

use yii\db\Migration;

/**
 * Class m230314_182520_subjects_access
 */
class m230314_182520_subjects_access extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create subjects_access table
        $this->createTable('subjects_access', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'access_type' => $this->integer()->notNull(),
        ]);

        // add foreign keys
        $this->addForeignKey('subjects_access_ibfk_1', 'subjects_access', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('subjects_access_ibfk_2', 'subjects_access', 'subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');

        // add indexes
        $this->createIndex('subjects_access_ibfk_1', 'subjects_access', 'user_id');
        $this->createIndex('subjects_access_ibfk_2', 'subjects_access', 'subject_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drop foreign keys
        $this->dropForeignKey('subjects_access_ibfk_1', 'subjects_access');
        $this->dropForeignKey('subjects_access_ibfk_2', 'subjects_access');

        // drop indexes
        $this->dropIndex('subjects_access_ibfk_1', 'subjects_access');
        $this->dropIndex('subjects_access_ibfk_2', 'subjects_access');

        // drop table
        $this->dropTable('subjects_access');
    }
}
