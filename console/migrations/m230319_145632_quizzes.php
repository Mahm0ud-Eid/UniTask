<?php

use yii\db\Migration;

/**
 * Class m230319_145632_quizzes
 */
class m230319_145632_quizzes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('quizzes', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'active' => $this->boolean()->notNull(),
            'duration' => $this->integer()->notNull(),
            'results_visibility' => $this->boolean()->notNull(),
            'department' => $this->integer()->notNull(),
            'semester' => $this->integer()->notNull(),
        ]);
        // creates index for column `user_id` and foreign key
        $this->createIndex(
            'idx-quizzes-user_id',
            'quizzes',
            'user_id'
        );
        $this->addForeignKey(
            'fk-quizzes-user_id',
            'quizzes',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE',
        );
        // forgein key for department and semester
        $this->addForeignKey(
            'fk-quizzes-department',
            'quizzes',
            'department',
            'departments',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk-quizzes-semester',
            'quizzes',
            'semester',
            'years',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('questions', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'question_text' => $this->string()->notNull(),
            'question_type' => $this->string()->Null(),
            'options' => $this->string()->Null(),
            'correct_answer' => $this->string()->Null(),
            'grade' => $this->integer()->Null(),
            'difficulty' => $this->string()->Null(),
        ]);
        // creates index for column `user_id` and foreign key
        $this->createIndex(
            'idx-questions-user_id',
            'questions',
            'user_id'
        );
        $this->addForeignKey(
            'fk-questions-user_id',
            'questions',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('quiz_question', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
        ]);
        // creates index for column `quiz_id` and foreign key
        $this->createIndex(
            'idx-quiz_question-quiz_id',
            'quiz_question',
            'quiz_id'
        );
        $this->addForeignKey(
            'fk-quiz_question-quiz_id',
            'quiz_question',
            'quiz_id',
            'quizzes',
            'id',
            'CASCADE',
            'CASCADE',
        );

        // creates index for column `question_id` and foreign key
        $this->createIndex(
            'idx-quiz_question-question_id',
            'quiz_question',
            'question_id'
        );
        $this->addForeignKey(
            'fk-quiz_question-question_id',
            'quiz_question',
            'question_id',
            'questions',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('quiz_attempt', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'answers' => $this->string()->notNull(),
            'grade' => $this->integer()->notNull(),
        ]);
        // creates index for column `quiz_id` and foreign key
        $this->createIndex(
            'idx-quiz_attempt-quiz_id',
            'quiz_attempt',
            'quiz_id'
        );
        $this->addForeignKey(
            'fk-quiz_attempt-quiz_id',
            'quiz_attempt',
            'quiz_id',
            'quizzes',
            'id',
            'CASCADE',
            'CASCADE',
        );

        // creates index for column `user_id` and foreign key
        $this->createIndex('idx-quiz_attempt-user_id', 'quiz_attempt', 'user_id');
        $this->addForeignKey(
            'fk-quiz_attempt-user_id',
            'quiz_attempt',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drop forgein keys and indexes
        $this->dropForeignKey('fk-quizzes-user_id', 'quizzes');
        $this->dropIndex('idx-quizzes-user_id', 'quizzes');
        $this->dropForeignKey('fk-quizzes-department', 'quizzes');
        $this->dropForeignKey('fk-quizzes-semester', 'quizzes');
        $this->dropForeignKey('fk-questions-user_id', 'questions');
        $this->dropIndex('idx-questions-user_id', 'questions');
        $this->dropForeignKey('fk-quiz_question-quiz_id', 'quiz_question');
        $this->dropIndex('idx-quiz_question-quiz_id', 'quiz_question');
        $this->dropForeignKey('fk-quiz_question-question_id', 'quiz_question');
        $this->dropIndex('idx-quiz_question-question_id', 'quiz_question');
        $this->dropForeignKey('fk-quiz_attempt-quiz_id', 'quiz_attempt');
        $this->dropIndex('idx-quiz_attempt-quiz_id', 'quiz_attempt');
        $this->dropForeignKey('fk-quiz_attempt-user_id', 'quiz_attempt');
        $this->dropIndex('idx-quiz_attempt-user_id', 'quiz_attempt');
        // drop tables
        $this->dropTable('quizzes');
        $this->dropTable('questions');
        $this->dropTable('quiz_question');
        $this->dropTable('quiz_attempt');

        return true;
    }
}
