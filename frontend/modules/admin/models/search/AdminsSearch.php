<?php

namespace frontend\modules\admin\models\search;

use common\models\User;
use yii\data\ActiveDataProvider;

class AdminsSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'email'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = User::find()->where(['is_teacher' => 0, 'is_admin' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
