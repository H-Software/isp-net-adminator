<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

// https://github.com/cartalyst/sentinel/blob/7.x/src/migrations/2014_07_02_230147_migration_cartalyst_sentinel.php

class AddColumnsForNameToUsersTable extends Migration
{
    public function up()
    {
        $this->schema->table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
        });
    }

    public function down()
    {
        $this->schema->table('users', function($table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });
    }
}
