<?php

namespace frontend\controllers;

use common\enum\PermissionType;
use common\models\Materials;
use common\models\Subjects;
use common\utils\FileSizeUtils;
use common\utils\TimeUtils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Materials controller for student
 */
class MaterialsController extends Controller
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
                        'actions' => ['index', 'view', 'download-file', 'download-subject', 'download-lecture'],
                        'roles' => [PermissionType::STUDENTS],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays index page for materials available to student.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $subjects = Subjects::find()->where([
            'department_id' => Yii::$app->user->identity->studentDepartmentYears->department_id,
            'semester_id' => Yii::$app->user->identity->studentDepartmentYears->semester_id,
        ])->all();
        $subjectIds = ArrayHelper::getColumn($subjects, 'id');
        $materials = Materials::find()->where(['subject_id' => $subjectIds])->all();

        return $this->render('index.twig', [
            'subjects' => $subjects,
            'materials' => $materials,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function securityChecks($subject_id, $material_id, $file_id = null)
    {
        $subject = Subjects::find()
            ->andWhere([
                'department_id' => Yii::$app->user->identity->studentDepartmentYears->department_id,
                'semester_id' => Yii::$app->user->identity->studentDepartmentYears->semester_id,
                'id' => $subject_id,
            ])
            ->one();

        if (!$subject) {
            throw new NotFoundHttpException('Error while loading subject');
        }

        $material = $subject->getMaterials()
            ->andWhere(['id' => $material_id])
            ->one();

        if (!$material) {
            throw new NotFoundHttpException('Error while loading material');
        }

        if ($file_id) {
            $file = $material->getFiles()
                ->andWhere(['file_id' => $file_id])
                ->one();
            if (!$file) {
                throw new NotFoundHttpException('Error while loading file');
            }

            return [$subject, $material, $file];
        }

        return [$subject, $material];
    }

    /**
     * Displays view page for material.
     *
     * @return mixed
     */
    public function actionView($id, $materialId)
    {
        $checks = $this->securityChecks($id, $materialId);
        $subject = $checks[0];
        $material = $checks[1];

        foreach ($material->files as $file) {
            $file->file->size = FileSizeUtils::getSize($file->file->size);
            $file->file->created_at = TimeUtils::getTimeAgo($file->file->created_at);
        }

        return $this->render('view.twig', [
            'subject' => $subject,
            'material' => $material,
        ]);
    }

    public function actionDownloadFile($id, $materialId, $fileId)
    {
        $checks = $this->securityChecks($id, $materialId, $fileId);
        $subject = $checks[0];
        $material = $checks[1];
        $file = $checks[2];
        $filePath = $file->file->getFilePath();
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, $file->file->name . '.' . $file->file->extension);
        }
        Yii::$app->session->setFlash('error', 'File not found');
        return $this->redirect(['materials/view', 'id' => $subject->id, 'materialId' => $material->id]);
    }

    public function actionDownloadLecture($id, $materialId)
    {
        $checks = $this->securityChecks($id, $materialId);
        $material = $checks[1];
        $files = $material->files;
        $cacheKey = 'lecture_zip_' . $material->id;
        $zipPath = Yii::$app->cache->get($cacheKey);
        if ($zipPath === false) {
            $zip = new \ZipArchive();
            $randomPath = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
            $zipPath = Yii::getAlias('@zips/') . $randomPath . $material->title . '.zip';
            mkdir(Yii::getAlias('@zips/') . $randomPath, 0755, true);
            $zip->open($zipPath, \ZipArchive::CREATE);
            foreach ($files as $file) {
                $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
                $zip->addFile($filePath, $file->file->name . '.' . $file->file->extension);
            }
            $zip->close();
            Yii::$app->cache->set($cacheKey, $zipPath, 60 * 60 * 24 * 7);
        }
        return Yii::$app->response->sendFile($zipPath, $material->title . '.zip');
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDownloadSubject($id)
    {
        $subject = Subjects::find()
            ->andWhere([
                'department_id' => Yii::$app->user->identity->studentDepartmentYears->department_id,
                'semester_id' => Yii::$app->user->identity->studentDepartmentYears->semester_id,
                'id' => $id,
            ])
            ->with('materials.files.file')
            ->one();

        if (!$subject) {
            throw new NotFoundHttpException('Subject not found.');
        }

        $cacheKey = 'subject_zip_' . $subject->id;
        $zipPath = Yii::$app->cache->get($cacheKey);

        if ($zipPath === false) {
            try {
                $zip = new \ZipArchive();
                $randomPath = Yii::$app->security->generateRandomString(90) . '/' . Yii::$app->security->generateRandomString(90) . '/';
                $zipPath = Yii::getAlias('@zips/') . $randomPath . $subject->name . '.zip';
                mkdir(Yii::getAlias('@zips/') . $randomPath, 0755, true);
                $zip->open($zipPath, \ZipArchive::CREATE);
                foreach ($subject->materials as $material) {
                    $folderName = $material->title . '/';
                    foreach ($material->files as $file) {
                        $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
                        $zip->addFile($filePath, $folderName . $file->file->name . '.' . $file->file->extension);
                    }
                }
                $zip->close();
                Yii::$app->cache->set($cacheKey, $zipPath, 60 * 60 * 24 * 7);
            } catch (\Exception $e) {
                throw new ServerErrorHttpException('Error while downloading subject.');
            }
        }

        return Yii::$app->response->sendFile($zipPath, $subject->name . '.zip')
            ->setStatusCode(200);
    }



    public static function getRoutes(): array
    {
        return [
            'materials' => 'materials/index',
            'materials/<id>/view/<materialId>' => 'materials/view',
            'materials/<id>/download/<materialId>/file/<fileId>' => 'materials/download-file',
            'materials/<id>/download-lecture/<materialId>' => 'materials/download-lecture',
            'materials/<id>/download-subject' => 'materials/download-subject',
        ];
    }
}
