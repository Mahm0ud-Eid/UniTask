<?php

namespace frontend\modules\teacher\models\tasks;

use common\models\Subjects;
use common\models\Tasks;
use Yii;
use yii\base\Model;

/**
 * Class AddTaskModel
 * @package common\models\tasks
 */
class AddTaskModel extends Model
{
    public $title;
    public $description;
    public $starts_at;
    public $ends_at;
    public $active;
    public $file_types;
    public $results_visibility;

    public function rules(): array
    {
        return [
            [['title', 'description', 'ends_at', 'active', 'file_types', 'results_visibility'], 'required'],
            [['title', 'description'], 'string'],
            [['ends_at', 'starts_at'], 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
            [['active'], 'boolean'],
        ];
    }

    /**
     * @return bool
     */
    public function add($id): bool
    {
        $task = new Tasks();
        $task->title = $this->title;
        $task->description = $this->description;
        $task->starts_at = date('Y-m-d H:i:s', strtotime($this->starts_at) - getenv('UTC_SECONDS'));
        $task->ends_at = date('Y-m-d H:i:s', strtotime($this->ends_at) - getenv('UTC_SECONDS'));
        $task->active = $this->active;
        $task->file_types = implode(',', $this->file_types);
        $task->results_visibility = $this->results_visibility;
        $task->created_at = date('Y-m-d H:i:s');
        $task->subject_id = $id;
        $subject = Subjects::find()->where(['id' => $id])->one();
        $task->department_id = $subject->department_id;
        $task->semester_id = $subject->semester_id;
        $task->user_id = Yii::$app->user->id;
        if ($task->save()) {
            return true;
        }
        return false;
    }
}
