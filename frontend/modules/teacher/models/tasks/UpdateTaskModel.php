<?php

namespace frontend\modules\teacher\models\tasks;

use common\models\Tasks;
use Yii;
use yii\base\Model;

/**
 * Class UpdateTaskModel
 * @package frontend\modules\teacher\models\tasks
 */
class UpdateTaskModel extends Model
{
    public $id;
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
     * @param int $id
     * @return bool
     */
    public function update(int $id): bool
    {
        $task = Tasks::findOne($id);
        if (!$task) {
            return false;
        }
        $task->title = $this->title;
        $task->description = $this->description;
        $task->starts_at = date('Y-m-d H:i:s', strtotime($this->starts_at) - getenv('UTC_SECONDS'));
        $task->ends_at = date('Y-m-d H:i:s', strtotime($this->ends_at) - getenv('UTC_SECONDS'));
        $task->active = $this->active;
        $task->file_types = implode(',', $this->file_types);
        $task->results_visibility = $this->results_visibility;
        if ($task->save()) {
            return true;
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function loadTask(int $id): bool
    {
        $task = Tasks::findOne($id);
        if (!$task) {
            return false;
        }
        $this->id = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->starts_at = date('Y-m-d H:i:s', strtotime($task->starts_at) + getenv('UTC_SECONDS'));
        $this->ends_at = date('Y-m-d H:i:s', strtotime($task->ends_at) + getenv('UTC_SECONDS'));
        $this->active = $task->active;
        $this->file_types = explode(',', $task->file_types);
        $this->results_visibility = $task->results_visibility;
        return true;
    }
}
