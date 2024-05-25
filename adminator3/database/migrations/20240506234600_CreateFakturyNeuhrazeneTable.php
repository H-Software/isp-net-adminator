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
            $table->string('Cislo', 50)->nullable();
            $table->string('VarSym', 50)->nullable();
            $table->date('Datum')->nullable();
            $table->date('DatSplat')->nullable();
            #
            $table->string('KcCelkem')->nullable();
            $table->string('KcLikv')->nullable();
            #
            $table->string('Firma')->nullable();
            $table->string('Jmeno')->nullable();
            $table->string('ICO')->nullable();
            $table->string('DIC')->nullable();
            #
            $table->integer('par_id_vlastnika')->unsigned()->nullable();
            $table->integer('par_stav')->nullable();
            #
            $table->date('datum_vlozeni')->nullable();
            $table->integer('overeno')->unsigned()->default(0)->nullable();
            #
            $table->string('aut_email_stav')->nullable();
            $table->date('aut_email_datum')->nullable();
            $table->string('aut_sms_stav')->nullable();
            $table->date('aut_sms_datum')->nullable();
            #
            $table->integer('ignorovat')->unsigned()->default(0)->nullable();
            $table->string('po_splatnosti_vlastnik', 50)->nullable();
        });
    }

    public function down()
    {
        $this->schema->drop('faktury_neuhrazene');
    }
}
