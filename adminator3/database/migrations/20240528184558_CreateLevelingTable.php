<?php

class CreateLevelingTable extends App\Migration\Migration
{
    public function up()
    {
        $this->table('leveling', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'identity' => 'enable',
            ])
            ->addColumn('level', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'id',
            ])
            ->addColumn('popis', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'level',
            ])
            ->addIndex(['id'], [
                'name' => 'id',
                'unique' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->schema->drop('leveling');
    }
}
