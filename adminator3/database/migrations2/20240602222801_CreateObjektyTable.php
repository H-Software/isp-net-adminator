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
                'after' => 'id_komplu',
            ])
            ->addColumn('dns_jmeno', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id_cloveka',
            ])
            ->addColumn('ip', 'biginteger', [
                'null' => false,
                'limit' => 20,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'dns_jmeno',
            ])
            ->addColumn('mac', 'string', [
                'null' => false,
                'limit' => 150,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'ip',
            ])
            ->addColumn('typ', 'integer', [
                'null' => false,
                'signed' => false,
                'default' => '0',
                'after' => 'mac',
            ])
            ->addColumn('client_ap_ip', 'biginteger', [
                'null' => true,
                'limit' => 20,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'typ',
            ])
            ->addColumn('verejna', 'integer', [
                'null' => true,
                'signed' => false,
                'after' => 'client_ip_ap',
            ])
            ->addColumn('id_tridy', 'integer', [
                'null' => true,
                'signed' => false,
                'after' => 'verejna',
            ])
            ->addColumn('id_nodu', 'integer', [
                'null' => true,
                'signed' => false,
                'after' => 'id_tridy',
            ])
            ->addColumn('id_tarifu', 'integer', [
                'null' => true,
                'signed' => false,
                'after' => 'id_nodu',
            ])
            ->addColumn('dov_net', 'string', [
                'null' => false,
                'limit' => 1,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'id_tarifu',
                'default' => '1',
            ])
            ->addColumn('sikana_status', 'string', [
                'null' => false,
                'limit' => 1,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'dov_net',
                'default' => '1',
            ])
            ->addColumn('sikana_text', 'string', [
                'null' => true,
                'limit' => 250,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'sikana_status',
            ])
            ->addColumn('sikana_cas', 'integer', [
                'null' => false,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'sikana_status',
            ])
            ->addColumn('pridano', 'timestamp', [
                'null' => true,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'sikana_cas',
            ])
            ->addColumn('vip_snat', 'integer', [
                'null' => false,
                'limit' => 1,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'pridano',
                'default' => '0',
            ])
            ->addColumn('tunnelling_ip', 'string', [
                'null' => true,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'vip_snat',
            ])
            ->addColumn('poznamka', 'string', [
                'null' => true,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'tunnelling_ip',
                'limit' => 4096,
            ])
            ->addColumn('pridal', 'string', [
                'null' => true,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'poznamka',
            ])
            ->addColumn('upravil', 'string', [
                'null' => true,
                'limit' => 50,
                'collation' => 'utf8mb3_unicode_ci',
                'encoding' => 'utf8mb3',
                'after' => 'pridal',
            ])
            # TODO: add the rest of columns
            # port_id - int
            # another_vlad_id - int, null
            # tunnel_user - char, 50, null
            # tunnel_pass - char, 50, null

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
