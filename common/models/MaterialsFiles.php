<?php

namespace common\models;

use cranky4\changeLogBehavior\ChangeLogBehavior;
use Yii;

/**
 * This is the model class for table "materials_files".
 *
 * @property int $id
 * @property int $material_id
 * @property int $file_id
 *
 * @property Files $file
 * @property Materials $material
 */
class MaterialsFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_id', 'file_id'], 'required'],
            [['material_id', 'file_id'], 'integer'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Materials::class, 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'material_id' => Yii::t('app', 'Material ID'),
            'file_id' => Yii::t('app', 'File ID'),
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
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * Gets query for [[Material]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Materials::class, ['id' => 'material_id']);
    }
}
