<?php

namespace frontend\modules\admin\models\auth;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_rule".
 *
 * @property string $name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthItem[] $authItems
 */
class AuthRule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name'       => Yii::t('models', 'Name'),
            'data'       => Yii::t('models', 'Data'),
            'created_at' => Yii::t('models', 'Created At'),
            'updated_at' => Yii::t('models', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[AuthItems]].
     *
     * @return ActiveQuery
     */
    public function getAuthItems(): ActiveQuery
    {
        return $this->hasMany(AuthItem::class, ['rule_name' => 'name']);
    }
}
