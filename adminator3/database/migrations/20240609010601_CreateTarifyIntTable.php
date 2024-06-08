<?php

class CreateTarifyIntTable extends App\Migration\Migration
{
    public function up()
    {
        $this->table('tarify_int', [
                'id' => false,
                'primary_key' => ['id_tarifu'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id_tarifu', 'integer', [
                'null' => false,
                'identity' => 'enable',
            ])

            ->addColumn('typ_tarifu', 'integer', [
                'null' => false,
                'signed' => false,
                'after' => 'id_tarifu',
            ])
            ->addIndex(['id_tarifu'], [
                'name' => 'id_tarifu_unique',
                'unique' => true,
            ])
            // TODO: add the rest of the columns
            
            ->create();
    }

    public function down()
    {
        $this->schema->drop('tarify_int');
    }
}
