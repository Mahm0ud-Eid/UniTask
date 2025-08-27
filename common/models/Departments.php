<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "departments".
 *
 * @property int $id
 * @property string $name
 *
 * @property Quizzes[] $quizzes
 * @property StudentDepartmentYear[] $studentDepartmentYears
 * @property Subjects[] $subjects
 * @property Tasks[] $tasks
 */
class Departments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * Gets query for [[Quizzes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizzes()
    {
        return $this->hasMany(Quizzes::class, ['department_id' => 'id']);
    }

    /**
     * Gets query for [[StudentDepartmentYears]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentDepartmentYears()
    {
        return $this->hasMany(StudentDepartmentYear::class, ['department_id' => 'id']);
    }

    /**
     * Gets query for [[Subjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjects()
    {
        return $this->hasMany(Subjects::class, ['department_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['department_id' => 'id']);
    }
}
