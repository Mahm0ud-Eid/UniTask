<?php

use common\enum\PermissionType;
use yii\db\Migration;

/**
 * Class m230324_190130_add_quizzes_permission
 */
class m230324_190130_add_quizzes_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $createQuizzes = $auth->createPermission(PermissionType::CREATE_QUIZZES);
        $createQuizzes->description = 'Create quizzes - teacher';
        $auth->add($createQuizzes);

        $role = $auth->getRole('teachers');
        $auth->addChild($role, $createQuizzes);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230324_190130_add_quizzes_permission cannot be reverted.\n";

        return false;
    }
}
