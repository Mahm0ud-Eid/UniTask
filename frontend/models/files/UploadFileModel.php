<?php

namespace frontend\models\files;

use common\models\TaskAttempt;
use common\models\TaskAttemptFiles;
use common\models\Tasks;
use common\models\Files;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\helpers\HtmlPurifier;
use yii\web\UploadedFile;

/**
 * Class UploadFileModel
 * @package frontend\models\files
 */
class UploadFileModel extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    /**
     * @var string
     */
    public $student_comment;

    /**
     * @var Tasks
     */
    public $task;

    /**
     * @var TaskAttempt
     */
    public $taskAttempt;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => $this->task->file_types,
                'message' => 'Only files with these extensions are allowed: {extensions}',
                'checkExtensionByMimeType' => false,
                'maxFiles' => 10,
                'maxSize' => 1024 * 1024 * 10,
            ],
            [['student_comment'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function upload(): bool
    {
        $taskAttempt = $this->taskAttempt;
        $taskAttempt->student_comment = HtmlPurifier::process($this->student_comment);
        $taskAttempt->save();
        $path = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
        foreach ($this->files as $file) {
            if ($file instanceof UploadedFile && $file->size > 0 && $file->error === UPLOAD_ERR_OK) {
                if (!$this->validate()) {
                    Yii::$app->session->setFlash('error', 'Failed to validate file');
                    return false;
                }
                // Check if file already exists under the same task attempt
                if (TaskAttemptFiles::find()->where(['task_attempt_id' => $taskAttempt->id,
                    'file_id' => Files::find()->where(['name' => $file->baseName, 'extension' => $file->extension,
                        'user_id' => Yii::$app->user->id
                    ])->one()])->exists()) {
                    Yii::$app->session->setFlash('error', 'File already exists. 
                        Delete the file from files tab and try again');
                    return false;
                }
                $fileModel = new Files();
                $fileModel->name = $file->baseName;
                $fileModel->extension = $file->extension;
                $fileModel->size = $file->size;
                $fileModel->path = $path;
                $fileModel->user_id = Yii::$app->user->id;
                $fileModel->created_at = new Expression('CURRENT_TIMESTAMP');

                try {
                    if (!$fileModel->save()) {
                        Yii::$app->session->setFlash('error', 'Error while saving file');
                        return false;
                    }
                    FileHelper::createDirectory(Yii::getAlias('@uploads/') . $path . '/');
                    $file->saveAs(Yii::getAlias('@uploads/') . $path . $fileModel->id . '.' . $file->extension);

                    $taskFile = new TaskAttemptFiles();
                    $taskFile->task_attempt_id = $taskAttempt->id;
                    $taskFile->file_id = $fileModel->id;
                    if (!$taskFile->save()) {
                        Yii::$app->session->setFlash('error', 'Error while uploading file');
                        return false;
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error while uploading file: ' . $e->getMessage());
                    return false;
                }
            }
        }
        return true;
    }
}
