<?php

namespace frontend\modules\admin\models\search;

use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StudentsSearch extends User
{
    public $department;
    public $semester;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'email', 'department', 'semester'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = User::find()->joinWith(['studentDepartmentYears', 'studentDepartmentYears.department', 'studentDepartmentYears.semester'])
            ->where(['is_teacher' => 0, 'is_admin' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['department'] = [
            'asc' => ['departments.name' => SORT_ASC],
            'desc' => ['departments.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['semester'] = [
            'asc' => ['semesters.name' => SORT_ASC],
            'desc' => ['semesters.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'user.name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'departments.name', $this->department])
            ->andFilterWhere(['like', 'semesters.name', $this->semester]);

        return $dataProvider;
    }
}
