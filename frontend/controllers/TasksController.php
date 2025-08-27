<?php

namespace frontend\controllers;

use common\enum\PermissionType;
use common\models\QuizAttempt;
use common\models\Quizzes;
use common\models\TaskAttempt;
use common\models\Tasks;
use frontend\models\files\UploadFileModel;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * Tasks controller for student
 */
class TasksController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'delete-file', 'download-file', 'results'],
                        'roles' => [PermissionType::STUDENTS],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays index page for quizzes available to student.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $tasks = Yii::$app->user->identity->getAvailableTasks();
        // check if attempted
        $listTasks = [];
        foreach ($tasks as $task) {
            $task->ends_at = date('Y-m-d g:i:s A', strtotime($task->ends_at) + getenv('UTC_SECONDS'));
            $listTasks[] = $task;
        }
        return $this->render('index.twig', [
            'tasks' => $listTasks,
        ]);
    }


    /**
     * Displays view page for task.
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $task = Tasks::findOne($id);
        if ($task == null || ($task->department_id != Yii::$app->user->identity->studentDepartmentYears->department_id
            || Yii::$app->user->identity->studentDepartmentYears->semester_id != $task->semester_id)) {
            Yii::$app->session->setFlash('error', 'Task not found');
            return $this->redirect(['index']);
        }
        if (!$task->isAvailable()) {
            Yii::$app->session->setFlash('error', 'Task not available');
            return $this->redirect(['index']);
        }
        $uploadModel = new UploadFileModel();
        $uploadModel->task = $task;
        $attempt = TaskAttempt::find()->where(['task_id' => $id, 'user_id' => Yii::$app->user->id])->one();
        $uploadModel->taskAttempt = $attempt;
        if (empty($attempt)) {
            $attempt = new TaskAttempt();
            $attempt->task_id = $id;
            $attempt->user_id = Yii::$app->user->id;
            $attempt->save();
        }
        if ($this->request->isPost) {
            $uploadModel->files = UploadedFile::getInstances($uploadModel, 'files');
            $uploadModel->student_comment = $this->request->post('UploadFileModel')['student_comment'];
            if ($uploadModel->upload()) {
                Yii::$app->session->setFlash('success', 'File has been uploaded');
                return $this->redirect(['tasks/view', 'id' => $id]);
            }
        }
        return $this->render('view', [
            'task' => $task,
            'model' => $uploadModel,
            'attempt' => $attempt,
        ]);
    }

    private function securityChecks($id, $fileId)
    {
        $attempt = TaskAttempt::findOne($id);
        if ($attempt == null) {
            Yii::$app->session->setFlash('error', 'Task not found');
            return $this->redirect(['index']);
        }
        if (!$attempt->task->isAvailable()) {
            Yii::$app->session->setFlash('error', 'You cannot do this action');
            return $this->redirect(['index']);
        }
        $file = $attempt->getFiles()->where(['id' => $fileId])->one();
        if ($file == null) {
            Yii::$app->session->setFlash('error', 'File not found');
            return $this->redirect(['index']);
        }
        if ($file->file->user_id != Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot do this action');
            return $this->redirect(['index']);
        }
        return [$attempt, $file];
    }
    public function actionDeleteFile($id, $fileId)
    {
        $checks = $this->securityChecks($id, $fileId);
        $checks[1]->file->delete();
        Yii::$app->session->setFlash('success', 'File has been deleted');
        return $this->redirect(['tasks/view', 'id' => $checks[0]->task_id]);
    }

    public function actionDownloadFile($id, $fileId)
    {
        $checks = $this->securityChecks($id, $fileId);
        $file = $checks[1];
        $attempt = $checks[0];
        $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, $file->file->name . '.' . $file->file->extension);
        }
        Yii::$app->session->setFlash('error', 'File not found');
        return $this->redirect(['tasks/view', 'id' => $attempt->task_id]);
    }

    public function actionResults()
    {
        $results = TaskAttempt::findAll(['user_id' => Yii::$app->user->id]);
        $now = date('Y-m-d H:i:s');
        return $this->render('results.twig', [
            'results' => $results,
            'now' => $now,
        ]);
    }

    public static function getRoutes(): array
    {
        return [
            'tasks' => 'tasks/index',
            'tasks/<id>/view' => 'tasks/view',
            'tasks/<id>/delete-file/<fileId>' => 'tasks/delete-file',
            'tasks/<id>/download-file/<fileId>' => 'tasks/download-file',
            'tasks/results' => 'tasks/results',
        ];
    }
}
