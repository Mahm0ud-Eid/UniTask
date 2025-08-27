<?php

namespace common\models;

use common\enum\ConfirmationTokenType;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "confirmation_token".
 *
 * @property int $id
 * @property int $token_type
 * @property int $user_id
 * @property string|null $created_at
 * @property string $token
 *
 * @property User $user
 */
class ConfirmationToken extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'confirmation_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token_type', 'user_id', 'token'], 'required'],
            [['id', 'token_type', 'user_id'], 'integer'],
            [['token_type'], 'in', 'range' => array_keys(ConfirmationTokenType::LABEL)],
            [['created_at'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'token_type' => Yii::t('app', 'Token Type'),
            'user_id'    => Yii::t('app', 'User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'token'      => Yii::t('app', 'Token'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function beforeValidate()
    {
        if ($this->token === null) {
            $this->token = Yii::$app->security->generateRandomString(255);
        }
        return parent::beforeValidate();
    }


}
