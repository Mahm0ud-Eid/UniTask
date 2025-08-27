<?php

namespace frontend\modules\admin\models\auth;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 */
class AuthItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [
                ['rule_name'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => AuthRule::class,
                'targetAttribute' => ['rule_name' => 'name']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     */
    public function attributeLabels()
    {
        return [
            'name'        => Yii::t('models', 'Name'),
            'type'        => Yii::t('models', 'Type'),
            'description' => Yii::t('models', 'Description'),
            'rule_name'   => Yii::t('models', 'Rule Name'),
            'data'        => Yii::t('models', 'Data'),
            'created_at'  => Yii::t('models', 'Created At'),
            'updated_at'  => Yii::t('models', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return ActiveQuery
     */
    public function getAuthAssignments(): ActiveQuery
    {
        return $this->hasMany(AuthAssignment::class, ['item_name' => 'name']);
    }

    /**
     * Gets query for [[RuleName]].
     *
     * @return ActiveQuery
     */
    public function getRuleName(): ActiveQuery
    {
        return $this->hasOne(AuthRule::class, ['name' => 'rule_name']);
    }

    /**
     * Gets query for [[AuthItemChildren]].
     *
     * @return ActiveQuery
     */
    public function getAuthItemChildren(): ActiveQuery
    {
        return $this->hasMany(AuthItemChild::class, ['parent' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren0]].
     *
     * @return ActiveQuery
     */
    public function getAuthItemChildren0(): ActiveQuery
    {
        return $this->hasMany(AuthItemChild::class, ['child' => 'name']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['name' => 'child'])->viaTable(
            'auth_item_child',
            ['parent' => 'name']
        );
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getParents(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['name' => 'parent'])->viaTable(
            'auth_item_child',
            ['child' => 'name']
        );
    }
}
