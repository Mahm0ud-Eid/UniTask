<?php

namespace frontend\modules\teacher\models\files;

use common\models\Materials;
use common\models\MaterialsFiles;
use common\models\Files;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class UploadFileModel
 * @package frontend\modules\teacher\models\files
 */
class UploadFileModel extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;

    /**
     * @var Materials
     */
    public $material_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' =>
                'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,7z,mp4,mp3,avi,flv,wmv,webm,jpg,jpeg,png,gif',
                'message' => 'Only files with these extensions are allowed: {extensions}',
                'checkExtensionByMimeType' => false,
                'maxFiles' => 20
            ]
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function upload(): bool
    {
        $path = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
        foreach ($this->files as $file) {
            if ($file instanceof UploadedFile && $file->size > 0 && $file->error === UPLOAD_ERR_OK) {
                if (!$this->validate()) {
                    Yii::$app->session->setFlash('error', 'Failed to validate file');
                    return false;
                }
                //                if (Files::find()->where(['name' => $file->baseName, 'extension' => $file->extension, 'user_id' => Yii::$app->user->id])->exists()) {
                //                    Yii::$app->session->setFlash('error', 'File already exists.
                //                    Delete the file from files tab and try again');
                //                    return false;
                //                }
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

                    $materialFile = new MaterialsFiles();
                    $materialFile->material_id = $this->material_id;
                    $materialFile->file_id = $fileModel->id;
                    if (!$materialFile->save()) {
                        Yii::$app->session->setFlash('error', 'Error while uploading file: ');
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
