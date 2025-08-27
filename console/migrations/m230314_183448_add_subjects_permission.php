<?php

use yii\db\Migration;
use common\rules\SubjectsAccessRule;

/**
 * Class m230314_183448_add_subjects_permission
 */
class m230314_183448_add_subjects_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        // add SubjectsAccessRule to auth_rule
        $rule = new SubjectsAccessRule();
        $auth->add($rule);

        // add "viewSubjects" permission
        $viewSubjects = $auth->createPermission(\common\enum\PermissionType::MANAGE_SUBJECTS);
        $viewSubjects->description = 'Can manage subjects - teacher';
        $viewSubjects->ruleName = $rule->name;
        $auth->add($viewSubjects);

        $auth->addChild($auth->getRole('teachers'), $viewSubjects);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230314_183448_add_subjects_permission cannot be reverted.\n";

        return false;
    }
}
