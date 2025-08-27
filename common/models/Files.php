<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property int|null $size
 * @property string $created_at
 * @property int $user_id
 *
 * @property TaskAttempt[] $taskAttempts
 * @property User $user
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'path', 'extension', 'created_at', 'user_id'], 'required'],
            [['size', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'path', 'extension'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'path' => Yii::t('app', 'Path'),
            'extension' => Yii::t('app', 'Extension'),
            'size' => Yii::t('app', 'Size'),
            'created_at' => Yii::t('app', 'Created At'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::class,
            ],
        ];
    }

    /**
     * Gets query for [[TaskAttempts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAttempts()
    {
        return $this->hasMany(TaskAttempt::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        unlink(Yii::getAlias('@uploads/' . $this->path . $this->id . '.' . $this->extension));
        $path = Yii::getAlias('@uploads/' . $this->path);
        if (is_dir($path) && count(scandir($path)) == 2) {
            rmdir($path);
        }
        $parentDir = dirname(Yii::getAlias('@uploads/' . $this->path));
        if (count(scandir($parentDir)) == 2) {
            rmdir($parentDir);
        }
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploads/' . $this->path . $this->id . '.' . $this->extension);
    }
}
