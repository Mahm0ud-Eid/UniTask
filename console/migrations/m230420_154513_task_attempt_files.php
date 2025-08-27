<?php

use yii\db\Migration;

/**
 * Class m230420_154513_task_attempt_files
 */
class m230420_154513_task_attempt_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //drop foreign key fk_task_attempt_file
        $this->dropForeignKey('fk_task_attempt_file', 'task_attempt');
        //drop index idx_task_attempt_file
        $this->dropIndex('idx_task_attempt_file', 'task_attempt');

        $this->dropColumn('task_attempt', 'file_id');

        // create table task_attempt_files
        $this->createTable('task_attempt_files', [
            'id' => $this->primaryKey(),
            'task_attempt_id' => $this->integer()->notNull(),
            'file_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_task_attempt_files_task_attempt', 'task_attempt_files', 'task_attempt_id');
        $this->addForeignKey(
            'fk_task_attempt_files_task_attempt',
            'task_attempt_files',
            'task_attempt_id',
            'task_attempt',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx_task_attempt_files_file', 'task_attempt_files', 'file_id');
        $this->addForeignKey(
            'fk_task_attempt_files_file',
            'task_attempt_files',
            'file_id',
            'files',
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
        $this->dropForeignKey('fk_task_attempt_files_task_attempt', 'task_attempt_files');
        $this->dropIndex('idx_task_attempt_files_task_attempt', 'task_attempt_files');

        $this->dropForeignKey('fk_task_attempt_files_file', 'task_attempt_files');
        $this->dropIndex('idx_task_attempt_files_file', 'task_attempt_files');

        $this->dropTable('task_attempt_files');

        $this->addColumn('task_attempt', 'file_id', $this->integer()->notNull());

        $this->createIndex('idx_task_attempt_file', 'task_attempt', 'file_id');
        $this->addForeignKey(
            'fk_task_attempt_file',
            'task_attempt',
            'file_id',
            'files',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
