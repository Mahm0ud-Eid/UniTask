<?php

use yii\db\Migration;

/**
 * Class m230314_175249_add_permissions
 */
class m230314_175249_add_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $viewTeachers = $auth->createPermission(\common\enum\PermissionType::VIEW_TEACHERS);
        $viewTeachers->description = 'View teachers tab';
        $auth->add($viewTeachers);

        $addTeachers = $auth->createPermission(\common\enum\PermissionType::ADD_TEACHERS);
        $addTeachers->description = 'Can add teachers';
        $auth->add($addTeachers);

        $viewSubjects = $auth->createPermission(\common\enum\PermissionType::VIEW_SUBJECTS);
        $viewSubjects->description = 'View subjects tab';
        $auth->add($viewSubjects);

        $addSubjects = $auth->createPermission(\common\enum\PermissionType::ADD_SUBJECTS);
        $addSubjects->description = 'Can add subjects';
        $auth->add($addSubjects);

        $viewDepartments = $auth->createPermission(\common\enum\PermissionType::VIEW_DEPARTMENTS);
        $viewDepartments->description = 'View departments tab';
        $auth->add($viewDepartments);

        $addDepartments = $auth->createPermission(\common\enum\PermissionType::ADD_DEPARTMENTS);
        $addDepartments->description = 'Can add departments';
        $auth->add($addDepartments);

        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $viewTeachers);
        $auth->addChild($admin, $addTeachers);
        $auth->addChild($admin, $viewSubjects);
        $auth->addChild($admin, $addSubjects);
        $auth->addChild($admin, $viewDepartments);
        $auth->addChild($admin, $addDepartments);

        $teachers = $auth->createRole('teachers');
        $auth->add($teachers);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230314_175249_add_permissions cannot be reverted.\n";

        return false;
    }
}
