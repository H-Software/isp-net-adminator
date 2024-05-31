<?php


class CreateNodListTable extends App\Migration\Migration
{
    public function up()
    {
        // $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb3';");
        // $this->execute("ALTER DATABASE COLLATE='utf8mb3_unicode_ci';");
        $this->table('nod_list', [
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
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id',
            ])
            ->addColumn('adresa', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'jmeno',
            ])
            ->addColumn('pozn', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'adresa',
            ])
            ->addColumn('ip_rozsah', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'pozn',
            ])
            ->addColumn('typ_nodu', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'ip_rozsah',
            ])
            ->addColumn('typ_vysilace', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'typ_nodu',
            ])
            ->addColumn('stav', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'typ_vysilace',
            ])
            ->addColumn('router_id', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'stav',
            ])
            ->addColumn('vlan_id', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'router_id',
            ])
            ->addColumn('filter_router_id', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'vlan_id',
            ])
            ->addColumn('device_type_id', 'integer', [
                'null' => false,
                'default' => '0',
                'signed' => false,
                'after' => 'filter_router_id',
            ])
            ->create();
    }

    public function down()
    {
        $this->schema->drop('nod_list');
    }
}
