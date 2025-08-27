<?php

use yii\db\Migration;

/**
 * Class m230413_120309_multiple_queues
 */
class m230413_120309_multiple_queues extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            'queue',
            [
                'id' => $this->primaryKey(),
                'channel' => $this->string(255)->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull()->defaultValue(0),
                'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
                'reserved_at' => $this->integer()->null()->defaultValue(null),
                'attempt' => $this->integer()->null()->defaultValue(null),
                'done_at' => $this->integer()->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('channel', 'queue', ['channel'], false);
        $this->createIndex('reserved_at', 'queue', ['reserved_at'], false);
        $this->createIndex('priority', 'queue', ['priority'], false);

        $this->createTable(
            'queue2',
            [
                'id' => $this->primaryKey(),
                'channel' => $this->string(255)->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull()->defaultValue(0),
                'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
                'reserved_at' => $this->integer()->null()->defaultValue(null),
                'attempt' => $this->integer()->null()->defaultValue(null),
                'done_at' => $this->integer()->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('channel', 'queue2', ['channel'], false);
        $this->createIndex('reserved_at', 'queue2', ['reserved_at'], false);
        $this->createIndex('priority', 'queue2', ['priority'], false);

        $this->createTable(
            'queue3',
            [
                'id' => $this->primaryKey(),
                'channel' => $this->string(255)->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull()->defaultValue(0),
                'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
                'reserved_at' => $this->integer()->null()->defaultValue(null),
                'attempt' => $this->integer()->null()->defaultValue(null),
                'done_at' => $this->integer()->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('channel', 'queue3', ['channel'], false);
        $this->createIndex('reserved_at', 'queue3', ['reserved_at'], false);
        $this->createIndex('priority', 'queue3', ['priority'], false);

        $this->createTable(
            'queue4',
            [
                'id' => $this->primaryKey(),
                'channel' => $this->string(255)->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull()->defaultValue(0),
                'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
                'reserved_at' => $this->integer()->null()->defaultValue(null),
                'attempt' => $this->integer()->null()->defaultValue(null),
                'done_at' => $this->integer()->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('channel', 'queue4', ['channel'], false);
        $this->createIndex('reserved_at', 'queue4', ['reserved_at'], false);
        $this->createIndex('priority', 'queue4', ['priority'], false);

        $this->createTable(
            'queue5',
            [
                'id' => $this->primaryKey(),
                'channel' => $this->string(255)->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull()->defaultValue(0),
                'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
                'reserved_at' => $this->integer()->null()->defaultValue(null),
                'attempt' => $this->integer()->null()->defaultValue(null),
                'done_at' => $this->integer()->null()->defaultValue(null),
            ],
            $tableOptions
        );
        $this->createIndex('channel', 'queue5', ['channel'], false);
        $this->createIndex('reserved_at', 'queue5', ['reserved_at'], false);
        $this->createIndex('priority', 'queue5', ['priority'], false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('queue1');
        $this->dropTable('queue2');
        $this->dropTable('queue3');
        $this->dropTable('queue4');
        $this->dropTable('queue5');
        return true;
    }
}
