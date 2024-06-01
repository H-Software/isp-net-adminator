<?php

class CreateKategorieTable extends App\Migration\Migration
{
    public function up()
    {
        $this->table('kategorie', [
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
            ->addColumn('jmeno', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id',
            ])
            ->addColumn('sablona', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'jmeno',
            ])
            ->addIndex(['id'], [
                'name' => 'id_kategorie_unique',
                'unique' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->schema->drop('kategorie');
    }
}
