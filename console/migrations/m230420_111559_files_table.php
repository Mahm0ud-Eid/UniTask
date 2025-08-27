<?php

use yii\db\Migration;

/**
 * Class m230420_111559_files_table
 */
class m230420_111559_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'path' => $this->string(255)->notNull(),
            'extension' => $this->string(255)->notNull(),
            'size' => $this->integer()->NULL()->defaultValue(null),
            'created_at' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'user_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_files_user', 'files', 'user_id');
        $this->addForeignKey(
            'fk_files_user',
            'files',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('task_attempt', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'task_id' => $this->integer()->notNull(),
            'file_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull()->defaultValue(time()),
            'grade' => $this->integer()->NULL()->defaultValue(null),
            'student_comment' => $this->text()->NULL()->defaultValue(null),
            'teacher_comment' => $this->text()->NULL()->defaultValue(null),
        ]);

        $this->createIndex('idx_task_attempt_user', 'task_attempt', 'user_id');
        $this->addForeignKey(
            'fk_task_attempt_user',
            'task_attempt',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx_task_attempt_task', 'task_attempt', 'task_id');
        $this->addForeignKey(
            'fk_task_attempt_task',
            'task_attempt',
            'task_id',
            'tasks',
            'id',
            'CASCADE',
            'CASCADE'
        );

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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_files_user', 'files');
        $this->dropIndex('idx_files_user', 'files');
        $this->dropTable('files');

        $this->dropForeignKey('fk_task_attempt_user', 'task_attempt');
        $this->dropIndex('idx_task_attempt_user', 'task_attempt');
        $this->dropForeignKey('fk_task_attempt_task', 'task_attempt');
        $this->dropIndex('idx_task_attempt_task', 'task_attempt');
        $this->dropForeignKey('fk_task_attempt_file', 'task_attempt');
        $this->dropIndex('idx_task_attempt_file', 'task_attempt');
        $this->dropTable('task_attempt');
        return true;
    }
}
