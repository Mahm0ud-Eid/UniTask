<?php

namespace frontend\models\site;

use common\enum\ConfirmationTokenType;
use common\models\ConfirmationToken;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Class PasswordResetModel
 * @package frontend\models\user
 */
class PasswordResetModel extends Model
{
    public $email;

    public function rules(): array
    {
        return [
            [['email'], 'required'],
        ];
    }

    public function execute()
    {
        $user = User::findOne(['email' => $this->email]);
        if ($user !== null) {
            $confirmationToken = new ConfirmationToken();
            $confirmationToken->user_id = $user->id;
            $confirmationToken->token_type = ConfirmationTokenType::PASSWORD_RESET_CONFIRMATION;
            if ($confirmationToken->save()) {
                Yii::$app->mailer->compose('password-reset-request', ['token' => $confirmationToken->token])
                    ->setFrom([getenv('SMTP_FROM') => getenv('SMTP_FROM_NAME')])
                    ->setTo($confirmationToken->user->email)
                    ->setSubject(Yii::t('app', 'UniTask - Password Reset Request'))
                    ->send();
            }
        }


        return true;
    }


}
