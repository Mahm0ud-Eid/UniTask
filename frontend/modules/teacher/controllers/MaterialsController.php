<?php

namespace frontend\modules\teacher\controllers;

use common\enum\AttemptStatusEnum;
use common\enum\PermissionType;
use common\models\Files;
use common\models\Materials;
use common\models\MaterialsFiles;
use common\models\StudentDepartmentYear;
use common\models\Subjects;
use common\models\TaskAttempt;
use common\models\Tasks;
use common\models\User;
use frontend\modules\teacher\models\files\UploadFileModel;
use frontend\modules\teacher\models\tasks\AddTaskModel;
use frontend\modules\teacher\models\tasks\UpdateTaskModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * MaterialsController implements the CRUD actions for Materials model.
 */
class MaterialsController extends Controller
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
                            'roles' => [PermissionType::CREATE_MATERIALS],
                        ],

                        [
                            'allow' => true,
                            'actions' => [
                                'view',
                                'create',
                                'update',
                                'delete-file',
                                'download-file',
                                'list',
                                'delete',
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
     * Displays material view page.
     * @param int $id Subject ID
     * @return string|Response
     */
    public function actionView($id, $material_id)
    {
        $material = Materials::find()->where(['id' => $material_id])->one();
        if ($material == null) {
            Yii::$app->session->setFlash('error', 'Task not found');
            return $this->redirect(['index']);
        }
        $uploadModel = new UploadFileModel();
        $uploadModel->material_id = $material_id;

        if ($material->load($this->request->post()) && $material->save()) {
            if ($this->request->isPost) {
                $uploadModel->files = UploadedFile::getInstances($uploadModel, 'files');
                if ($uploadModel->upload()) {
                    $cacheKey = 'lecture_zip_' . $material_id;
                    $cacheKey2 = 'subject_zip_' . $id;
                    Yii::$app->cache->delete($cacheKey);
                    Yii::$app->cache->delete($cacheKey2);
                    Yii::$app->session->setFlash('success', 'Material has been updated');
                    return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $material_id]);
                }
            }
            $cacheKey = 'lecture_zip_' . $material_id;
            Yii::$app->cache->delete($cacheKey);
            Yii::$app->session->setFlash('success', 'Material has been updated');
            return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $material_id]);
        }

        return $this->render('view', [
            'material' => $material,
            'model' => $uploadModel,
        ]);
    }


    public function actionCreate($id)
    {
        $model = new Materials();
        $model->subject_id = $id;
        $model->user_id = Yii::$app->user->id;
        $model->created_at = date('Y-m-d H:i:s');
        $uploadModel = new UploadFileModel();

        if ($model->load($this->request->post()) && $model->save()) {
            $uploadModel->material_id = $model->id; // Assign the material ID to the UploadFileModel
            if ($this->request->isPost) {
                $uploadModel->files = UploadedFile::getInstances($uploadModel, 'files');
                if ($uploadModel->upload()) {
                    Yii::$app->session->setFlash('success', 'File has been uploaded');
                    return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $model->id]);
                }
            }
            Yii::$app->session->setFlash('success', 'Material has been created');
            return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $model->id]);
        }

        return $this->render('create', [
            'modelMaterial' => $model,
            'model' => $uploadModel,
            'subject' => Subjects::findOne($id),
        ]);
    }

    public function actionDeleteFile($id, $fileId)
    {
        $file = MaterialsFiles::find()->where(['id' => $fileId])->one();
        if ($file == null) {
            Yii::$app->session->setFlash('error', 'File not found');
            return $this->redirect(['index']);
        }
        $file->file->delete();
        $cacheKey = 'lecture_zip_' . $file->material_id;
        $cacheKey2 = 'subject_zip_' . $id;
        Yii::$app->cache->delete($cacheKey);
        Yii::$app->cache->delete($cacheKey2);
        Yii::$app->session->setFlash('success', 'File has been deleted');
        return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $file->material_id]);
    }

    public function actionDownloadFile($id, $fileId)
    {
        $file = MaterialsFiles::find()->where(['id' => $fileId])->one();
        if ($file == null) {
            Yii::$app->session->setFlash('error', 'File not found');
            return $this->redirect(['index']);
        }
        $filePath = Yii::getAlias('@uploads/') . $file->file->path . $file->file->id . '.' . $file->file->extension;
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, $file->file->name . '.' . $file->file->extension);
        }
        Yii::$app->session->setFlash('error', 'File not found');
        return $this->redirect(['materials/view', 'id' => $id, 'material_id' => $file->material_id]);
    }

    public function actionList($id)
    {
        $materials = Materials::find()->where(['subject_id' => $id])->all();
        $filesCount = [];
        foreach ($materials as $material) {
            $filesCount[$material->id] = MaterialsFiles::find()->where(['material_id' => $material->id])->count();
        }
        return $this->render('list.twig', [
            'materials' => $materials,
            'filesCount' => $filesCount,
            'subject' => Subjects::findOne($id),
        ]);
    }

    public function actionDelete($id, $materialId)
    {
        $material = Materials::find()->where(['id' => $materialId])->one();
        if ($material == null) {
            Yii::$app->session->setFlash('error', 'Material not found');
            return $this->redirect(['index']);
        }
        $files = $material->files;
        foreach ($files as $file) {
            $file->file->delete();
        }
        $cacheKey = 'lecture_zip_' . $material->id;
        $cacheKey2 = 'subject_zip_' . $id;
        Yii::$app->cache->delete($cacheKey);
        Yii::$app->cache->delete($cacheKey2);
        $material->delete();
        Yii::$app->session->setFlash('success', 'Material has been deleted');
        return $this->redirect(['materials/', 'id' => $id]);
    }


    public static function getRoutes()
    {
        return [
            'teacher/materials' => 'teacher/materials/index',
            'teacher/materials/<id>/view/<material_id>' => 'teacher/materials/view',
            'teacher/materials/<id>/create' => 'teacher/materials/create',
            'teacher/materials/<id>/delete-file/<fileId>' => 'teacher/materials/delete-file',
            'teacher/materials/<id>/download-file/<fileId>' => 'teacher/materials/download-file',
            'teacher/materials/<id>' => 'teacher/materials/list',
            'teacher/materials/<id>/delete-all/<materialId>' => 'teacher/materials/delete',
        ];
    }
}
