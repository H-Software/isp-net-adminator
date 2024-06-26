<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

// https://github.com/cartalyst/sentinel/blob/7.x/src/migrations/2014_07_02_230147_migration_cartalyst_sentinel.php

/**
 * Class CreatePersistencesTable.
 */
class CreatePersistencesTable extends Migration
{
    public function up()
    {
        $this->schema->create('users_persistences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('code')->unique();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        $this->schema->drop('users_persistences');
    }
}