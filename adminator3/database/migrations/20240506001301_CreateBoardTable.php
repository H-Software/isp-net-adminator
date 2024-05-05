<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoardTable extends Migration
{
    public function up()
    {
        $this->schema->create('board', function (Blueprint $table) {
            $table->increments('id');
            $table->string('author', 50);
            $table->string('email', 50);
            $table->date('from_date');
            $table->date('to_date');
            $table->string('subject', 150);
            $table->string('body', 4096);
        });
    }

    public function down()
    {
        $this->schema->drop('board');
    }
}