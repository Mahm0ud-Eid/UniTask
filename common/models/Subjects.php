<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subjects".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $credits
 * @property int $department_id
 * @property int $semester_id
 *
 * @property Departments $department
 * @property Materials[] $materials
 * @property Quizzes[] $quizzes
 * @property Semesters $semester
 * @property SubjectsAccess[] $subjectsAccesses
 * @property Tasks[] $tasks
 */
class Subjects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subjects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'credits', 'department_id', 'semester_id'], 'required'],
            [['description'], 'string'],
            [['credits', 'department_id', 'semester_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['department_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semesters::class, 'targetAttribute' => ['semester_id' => 'id']],
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
            'description' => Yii::t('app', 'Description'),
            'credits' => Yii::t('app', 'Credits'),
            'department_id' => Yii::t('app', 'Department ID'),
            'semester_id' => Yii::t('app', 'Semester ID'),
        ];
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['id' => 'department_id']);
    }

    /**
     * Gets query for [[Materials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Materials::class, ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Quizzes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuizzes()
    {
        return $this->hasMany(Quizzes::class, ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Semester]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemester()
    {
        return $this->hasOne(Semesters::class, ['id' => 'semester_id']);
    }

    /**
     * Gets query for [[SubjectsAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectsAccesses()
    {
        return $this->hasMany(SubjectsAccess::class, ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['subject_id' => 'id']);
    }
}
