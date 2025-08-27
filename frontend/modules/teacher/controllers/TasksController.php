<?php

namespace frontend\modules\teacher\controllers;

use common\enum\AttemptStatusEnum;
use common\enum\PermissionType;
use common\models\StudentDepartmentYear;
use common\models\Subjects;
use common\models\TaskAttempt;
use common\models\Tasks;
use common\models\User;
use frontend\modules\teacher\models\tasks\AddTaskModel;
use frontend\modules\teacher\models\tasks\UpdateTaskModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * TasksController implements the CRUD actions for Tasks model.
 */
class TasksController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index'],
                            'roles' => [PermissionType::CREATE_TASKS],
                        ],

                        [
                            'allow' => true,
                            'actions' => ['view', 'create', 'update',
                                'activate','deactivate','results',
                                'download-files',
                                'download-all-files',
                                'delete-files'
                            ],
                            'roles' => [PermissionType::MANAGE_SUBJECTS],
                            'roleParams' => ['subjectId' => Yii::$app->request->get('id')],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Tasks models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $subjects = Subjects::find()->joinWith('subjectsAccesses')
            ->where(['subjects_access.user_id' => Yii::$app->user->id])->all();
        return $this->render('index.twig', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Displays a single Tasks model.
     * @param int $id ID
     * @return string
     */
    public function actionView($id)
    {
        $tasks = Tasks::find()->where(['subject_id' => $id])->all();
        $tasks_count = [];
        foreach ($tasks as $task) {
            // add amount of students and amount of students that have taken the quiz
            $tasks_count[$task->id] = StudentDepartmentYear::find()->where(['department_id' => $task->department_id,
                'semester_id' => $task->semester_id])->count();
            $tasks_count[$task->id] = [
                'total' => $tasks_count[$task->id],
                'taken' => $task->taskAttempts ? TaskAttempt::find()
                    ->where(['task_id' => $task->id])
                    ->count() : 0,
            ];
        }
        return $this->render('view.twig', [
            'tasks' => $tasks,
            'subject' => Subjects::find()->where(['id' => $id])->one(),
            'tasks_count' => $tasks_count,
        ]);
    }

    /**
     * Creates a new Tasks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($id)
    {
        $model = new AddTaskModel();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->validate() && $model->add($id)) {
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tasks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     */
    public function actionUpdate($id, $task_id)
    {
        $model = new UpdateTaskModel();
        $model->loadTask($task_id);
        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->validate() && $model->update($task_id)) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionActivate($id, $task_id)
    {
        $task = Tasks::findOne($task_id);
        $task->active = 1;
        $task->save();
        Yii::$app->session->setFlash('success', 'Task activated successfully');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeactivate($id, $task_id)
    {
        $task = Tasks::findOne($task_id);
        $task->active = 0;
        $task->save();
        Yii::$app->session->setFlash('success', 'Task deactivated successfully');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionResults($id, $task_id)
    {
        $task = Tasks::findOne($task_id);
        $results = TaskAttempt::find()->where(['task_id' => $task_id])->all();
        $students = User::find()->joinWith('studentDepartmentYears')
            ->where(['student_department_year.department_id' => $task->department_id,
                'student_department_year.semester_id' => $task->semester_id])->all();
        foreach ($students as $student) {
            $found = false;
            foreach ($results as $result) {
                if ($result->user_id === $student->id) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $results[] = new TaskAttempt([
                    'user_id' => $student->id,
                    'task_id' => $id,
                    'grade' => 0,
                ]);
            }
        }
        if (Yii::$app->request->post('hasEditable') !== null) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $taskAttempt = TaskAttempt::findOne(['id' => Yii::$app->request->post('id')]);
            if ($taskAttempt !== null && $taskAttempt->task_id == $task_id) {
                $request = Yii::$app->request->post('TaskAttempt')[0];
                if (isset($request['teacher_comment'])) {
                    $taskAttempt->teacher_comment = Yii::$app->request->post('TaskAttempt')[0]['teacher_comment'];
                }
                if (isset($request['grade'])) {
                    $taskAttempt->grade = Yii::$app->request->post('TaskAttempt')[0]['grade'];
                }
                if ($taskAttempt->save()) {
                    return ['output' => '', 'message' => ''];
                }
            }
            return ['output' => '', 'message' => 'Save failed'];
        }
        return $this->render('results', [
            'results' => $results,
            'task' => $task,
        ]);
    }

    public function actionDownloadFiles($id, $attempt_id)
    {
        $attempt = TaskAttempt::findOne($attempt_id);
        $files = $attempt->files;
        $zip = new \ZipArchive();
        $randomPath = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
        $zipPath = Yii::getAlias('@zips/') . $randomPath . $attempt->user->name . '.zip';
        mkdir(Yii::getAlias('@zips/') . $randomPath, 0755, true);
        $zip->open($zipPath, \ZipArchive::CREATE);
        foreach ($files as $file) {
            $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
            $zip->addFile($filePath, $file->file->name . '.' . $file->file->extension);
        }
        $zip->close();
        return Yii::$app->response->sendFile($zipPath, $attempt->user->name . '.zip');
    }

    /**
     * @inheritdoc
     */
    public function actionDownloadAllFiles($id, $task_id)
    {
        // each student files should be in a folder with his name in the zip
        $task = Tasks::findOne($task_id);
        $attempts = TaskAttempt::find()->where(['task_id' => $task_id])->all();
        $zip = new \ZipArchive();
        $randomPath = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
        $zipPath = Yii::getAlias('@zips/') . $randomPath . $task->title . '.zip';
        mkdir(Yii::getAlias('@zips/') . $randomPath, 0755, true);
        $zip->open($zipPath, \ZipArchive::CREATE);
        foreach ($attempts as $attempt) {
            $files = $attempt->files;
            $folderPath = $attempt->user->name . '/';
            $zip->addEmptyDir($folderPath);
            foreach ($files as $file) {
                $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
                $zip->addFile($filePath, $folderPath . $file->file->name . '.' . $file->file->extension);
            }
        }
        $zip->close();
        return Yii::$app->response->sendFile($zipPath, $task->title . '.zip');
    }

    /**
     * @inheritdoc
     */
    public function actionDeleteFiles($id, $attempt_id)
    {
        $attempt = TaskAttempt::findOne($attempt_id);
        $files = $attempt->files;
        foreach ($files as $file) {
            $file->file->delete();
        }
        Yii::$app->session->setFlash('success', 'Files deleted successfully');
        return $this->redirect(['results', 'id' => $id, 'task_id' => $attempt->task_id]);
    }

    public static function getRoutes()
    {
        return [
            'teacher/tasks' => 'teacher/tasks/index',
            'teacher/tasks/<id>' => 'teacher/tasks/view',
            'teacher/tasks/<id>/create' => 'teacher/tasks/create',
            'teacher/tasks/<id>/update/<task_id>' => 'teacher/tasks/update',
            'teacher/tasks/<id>/activate/<task_id>' => 'teacher/tasks/activate',
            'teacher/tasks/<id>/deactivate/<task_id>' => 'teacher/tasks/deactivate',
            'teacher/tasks/<id>/results/<task_id>' => 'teacher/tasks/results',
            'teacher/tasks/<id>/download-all-files/<task_id>' => 'teacher/tasks/download-all-files',
            'teacher/tasks/<id>/download-files/<attempt_id>' => 'teacher/tasks/download-files',
            'teacher/tasks/<id>/delete-files/<attempt_id>' => 'teacher/tasks/delete-files',
        ];
    }
}
