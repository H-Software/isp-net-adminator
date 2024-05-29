<?php

class CreateRouterListTable extends App\Migration\Migration
{
    public function up()
    {
        $this->table('router_list', [
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
            ->addColumn('nazev', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id',
            ])
            ->addColumn('ip_adresa', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'nazev',
            ])
            ->addColumn('parent_router', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'ip_adresa',
            ])
            ->addColumn('mac', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'parent_router',
            ])
            ->addColumn('monitoring', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'mac',
            ])
            ->addColumn('monitoring_cat', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'monitoring',
            ])
            ->addColumn('alarm', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'monitoring_cat',
            ])
            ->addColumn('alarm_stav', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'alarm',
            ])
            ->addColumn('filtrace', 'integer', [
                'null' => false,
                'default' => '0',
                'after' => 'alarm_stav',
            ])
            ->addColumn('id_nodu', 'integer', [
                'null' => true,
                'after' => 'filtrace',
            ])
            ->addColumn('poznamka', 'string', [
                'null' => true,
                'limit' => 4096,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id_nodu',
            ])
            ->addColumn('warn', 'string', [
                'null' => true,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'poznamka',
            ])
            ->addColumn('mail', 'string', [
                'null' => true,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'warn',
            ])
            ->addIndex(['id'], [
                'name' => 'id_unique',
                'unique' => true,
            ])
            ->addIndex(['nazev'], [
                'name' => 'nazev',
                'unique' => true,
            ])
            ->addIndex(['ip_adresa'], [
                'name' => 'ip_adresa',
                'unique' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->schema->drop('router_list');
    }
}
