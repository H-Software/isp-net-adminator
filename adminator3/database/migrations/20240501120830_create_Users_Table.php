<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

// https://github.com/cartalyst/sentinel/blob/7.x/src/migrations/2014_07_02_230147_migration_cartalyst_sentinel.php

/**
 * Class CreateUsersTable.
 */
class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->integer('level')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('users');
    }
}
