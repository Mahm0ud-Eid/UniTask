<?php

namespace common\rules;

use common\enum\PermissionType;
use common\models\SubjectsAccess;
use Exception;
use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Class SubjectsAccessRule
 * @package common\rules
 */
class SubjectsAccessRule extends Rule
{
    /**
     * @inheritDoc
     */
    public $name = 'canManageSubjects';

    /**
     * @param int|string $user
     * @param Item $item
     * @param array $params
     *
     * @return bool
     */
    public function execute($user, $item, $params): bool
    {
        $access = SubjectsAccess::findOne(['subject_id' => $params['subjectId'], 'user_id' => $user]);
        if (!array_key_exists('minRole', $params)) {
            //User just needs access to the server
            return $access !== null;
        }

        //User needs to have at least a specific role
        if ($access === null) {
            return false;
        }
        if ($access->access_type < $params['minRole']) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function add()
    {
        $auth = Yii::$app->authManager;
        $auth->add($this);
    }
}
