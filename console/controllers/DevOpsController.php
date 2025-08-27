<?php

namespace console\controllers;

use common\jobs\ReviewAnswersJob;
use common\models\QuizAttempt;
use common\models\User;
use common\utils\QueueUtils;
use Exception;
use Yii;
use yii\console\Controller;

class DevOpsController extends Controller
{
    public function actionAddAdmin($name, $email, $password)
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->is_admin = 1;
        $user->generateApiKey();
        if ($user->save()) {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('admin');
            if ($role === null) {
                echo "Role admin does not exist";
                return;
            }
            $auth->assign($role, $user->id);
            echo "User $user->name is now admin";
        } else {
            echo "User could not be saved";
            echo json_encode($user->errors, JSON_THROW_ON_ERROR);
        }
    }

    public function actionBumpVersion($newVersion = null)
    {
        $composerJson = json_decode(file_get_contents('composer.json'), true, 512, JSON_THROW_ON_ERROR);
        $mainConfig = file_get_contents('common/config/main.php');
        $versionArray = explode('.', $composerJson['version']);
        if ($newVersion) {
            $newVersionArray = explode('.', $newVersion);
            $newVersion = implode('.', $newVersionArray);
        } else {
            $major = $versionArray[0];
            $minor = $versionArray[1];
            $patch = $versionArray[2];
            $patch++;
            $newVersionArray = [$major, $minor, $patch];
            $newVersion = implode('.', $newVersionArray);
        }
        $composerJson['version'] = $newVersion;
        $composerJson['date'] = date('Y-m-d');
        file_put_contents('composer.json', json_encode($composerJson, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        $mainConfig = str_replace("'" . implode('.', $versionArray) . "',//VERSION-HERE", "'" . $newVersion . "',//VERSION-HERE", $mainConfig);
        file_put_contents('common/config/main.php', $mainConfig);
        echo shell_exec('git add *');
        echo shell_exec('git commit -m "Build ' . $newVersion . '"');
        echo shell_exec('git tag ' . $newVersion);
        echo shell_exec('git push --all origin');
        echo shell_exec('git push --tags origin');
        echo 'Current version is ' . implode('.', $versionArray) . "\n";
        echo 'New version is ' . $newVersion . "\n";
        echo 'New release date is ' . $composerJson['date'] . "\n";
    }
}
