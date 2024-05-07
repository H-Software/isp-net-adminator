<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateFakturyFnImportLogTable extends Migration
{
    public function up()
    {
        $this->schema->create('fn_import_log', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->datetime('Datum');
        });
    }

    public function down()
    {
        $this->schema->drop('fn_import_log');
    }
}
