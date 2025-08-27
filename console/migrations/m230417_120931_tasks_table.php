<?php

use yii\db\Migration;

/**
 * Class m230417_120931_tasks_table
 */
class m230417_120931_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tasks', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'active' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ends_at' => $this->timestamp()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'results_visibility' => $this->integer()->notNull(),
            'department_id' => $this->integer()->notNull(),
            'semester_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'file_types' => $this->string(255),
        ]);

        $this->createIndex(
            'idx-tasks-user_id',
            'tasks',
            'user_id'
        );
        $this->createIndex(
            'idx-tasks-department_id',
            'tasks',
            'department_id'
        );
        $this->createIndex(
            'idx-tasks-semester_id',
            'tasks',
            'semester_id'
        );
        $this->createIndex(
            'idx-tasks-subject_id',
            'tasks',
            'subject_id'
        );

        $this->addForeignKey(
            'fk-tasks-user_id',
            'tasks',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-tasks-department_id',
            'tasks',
            'department_id',
            'departments',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-tasks-semester_id',
            'tasks',
            'semester_id',
            'semesters',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-tasks-subject_id',
            'tasks',
            'subject_id',
            'subjects',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $auth = Yii::$app->authManager;
        $createTasks = $auth->createPermission(\common\enum\PermissionType::CREATE_TASKS);
        $createTasks->description = 'Create tasks - teacher';
        $auth->add($createTasks);

        $teacher = $auth->getRole(\common\enum\PermissionType::TEACHERS);
        $auth->addChild($teacher, $createTasks);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-tasks-user_id',
            'tasks'
        );
        $this->dropIndex(
            'idx-tasks-department_id',
            'tasks'
        );
        $this->dropIndex(
            'idx-tasks-semester_id',
            'tasks'
        );
        $this->dropIndex(
            'idx-tasks-subject_id',
            'tasks'
        );

        $this->dropForeignKey(
            'fk-tasks-user_id',
            'tasks'
        );
        $this->dropForeignKey(
            'fk-tasks-department_id',
            'tasks'
        );
        $this->dropForeignKey(
            'fk-tasks-semester_id',
            'tasks'
        );
        $this->dropForeignKey(
            'fk-tasks-subject_id',
            'tasks'
        );

        $this->dropTable('tasks');
    }
}
