<?php

class CreateObjektyTable extends App\Migration\Migration
{
    public function up()
    {
        $this->table('objekty', [
                'id' => false,
                'primary_key' => ['id_komplu'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id_komplu', 'integer', [
                'null' => false,
                'identity' => 'enable',
            ])
            ->addColumn('id_cloveka', 'integer', [
                'null' => true,
                'signed' => false,
                'after' => 'jmeno',
            ])
            ->addColumn('dns_jmeno', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
            ])

            ->addIndex(['id_komplu'], [
                'name' => 'id_komplu_unique',
                'unique' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->schema->drop('objekty');
    }
}
