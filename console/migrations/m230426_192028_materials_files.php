<?php

use yii\db\Migration;

/**
 * Class m230426_192028_materials_files
 */
class m230426_192028_materials_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('materials', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'user_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-materials-user_id',
            'materials',
            'user_id'
        );
        $this->createIndex(
            'idx-materials-subject_id',
            'materials',
            'subject_id'
        );

        $this->addForeignKey(
            'fk-materials-user_id',
            'materials',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-materials-subject_id',
            'materials',
            'subject_id',
            'subjects',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $auth = Yii::$app->authManager;
        $createMaterials = $auth->createPermission(\common\enum\PermissionType::CREATE_MATERIALS);
        $createMaterials->description = 'Create materials - teacher';
        $auth->add($createMaterials);

        $teacher = $auth->getRole(\common\enum\PermissionType::TEACHERS);
        $auth->addChild($teacher, $createMaterials);

        // relation table between materials and files
        $this->createTable('materials_files', [
            'id' => $this->primaryKey(),
            'material_id' => $this->integer()->notNull(),
            'file_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-materials_files-material_id',
            'materials_files',
            'material_id'
        );
        $this->createIndex(
            'idx-materials_files-file_id',
            'materials_files',
            'file_id'
        );

        $this->addForeignKey(
            'fk-materials_files-material_id',
            'materials_files',
            'material_id',
            'materials',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-materials_files-file_id',
            'materials_files',
            'file_id',
            'files',
            'id',
            'CASCADE',
            'CASCADE',
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-materials_files-material_id', 'materials_files');
        $this->dropForeignKey('fk-materials_files-file_id', 'materials_files');

        $this->dropIndex('idx-materials_files-material_id', 'materials_files');
        $this->dropIndex('idx-materials_files-file_id', 'materials_files');

        $this->dropTable('materials_files');

        $this->dropForeignKey('fk-materials-user_id', 'materials');
        $this->dropForeignKey('fk-materials-subject_id', 'materials');

        $this->dropIndex('idx-materials-user_id', 'materials');
        $this->dropIndex('idx-materials-subject_id', 'materials');

        $this->dropTable('materials');

        $auth = Yii::$app->authManager;
        $createMaterials = $auth->getPermission(\common\enum\PermissionType::CREATE_MATERIALS);
        $auth->remove($createMaterials);

        return true;
    }
}
