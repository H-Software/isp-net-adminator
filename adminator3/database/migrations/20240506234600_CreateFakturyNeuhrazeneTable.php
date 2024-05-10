<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFakturyNeuhrazeneTable extends Migration
{
    public function up()
    {
        $this->schema->create('faktury_neuhrazene', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Cislo', 50);
            $table->string('VarSym', 50);
            $table->date('Datum');
            $table->date('DatSplat');
            $table->integer('par_id_vlastnika')->unsigned()->default(0);
            $table->integer('ignorovat')->unsigned()->default(0);
            $table->string('po_splatnosti_vlastnik', 50);
            // TODO: add the rest of columns
        });
    }

    public function down()
    {
        $this->schema->drop('faktury_neuhrazene');
    }
}
