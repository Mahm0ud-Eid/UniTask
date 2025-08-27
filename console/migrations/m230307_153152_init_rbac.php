<?php

use yii\db\Migration;

/**
 * Class m230307_153152_init_rbac
 */
class m230307_153152_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // add "createPost" permission
        $viewStudents = $auth->createPermission(\common\enum\PermissionType::VIEW_STUDENTS);
        $viewStudents->description = 'View students tab';
        $auth->add($viewStudents);

        $addStudents = $auth->createPermission(\common\enum\PermissionType::ADD_STUDENTS);
        $addStudents->description = 'Can add students';
        $auth->add($addStudents);

        // add "author" role and give this role the "createPost" permission
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $viewStudents);
        $auth->addChild($admin, $addStudents);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $students = $auth->createRole('students');
        $auth->add($students);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_153152_init_rbac cannot be reverted.\n";

        return false;
    }
}
