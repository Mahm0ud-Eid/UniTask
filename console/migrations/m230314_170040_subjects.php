<?php

use yii\db\Migration;

/**
 * Class m230314_170040_subjects
 */
class m230314_170040_subjects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('subjects', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'credits' => $this->integer()->notNull(),
            'department_id' => $this->integer()->notNull(),
            'semester_id' => $this->integer()->notNull(),
        ]);
        $this->createIndex('idx_subjects_id', 'subjects', 'id');
        $this->addForeignKey('fk_subjects_departments', 'subjects', 'department_id', 'departments', 'id');
        $this->addForeignKey('fk_subjects_semesters', 'subjects', 'semester_id', 'years', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_subjects_departments', 'subjects');
        $this->dropForeignKey('fk_subjects_semesters', 'subjects');
        $this->dropIndex('idx_subjects_id', 'subjects');
        $this->dropTable('subjects');
    }
}
