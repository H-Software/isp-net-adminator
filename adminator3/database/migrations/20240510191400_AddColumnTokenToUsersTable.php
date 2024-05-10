<?php
declare(strict_types=1);

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;


class AddColumnTokenToUsersTable extends Migration
{
    public function up()
    {
        $this->schema->table('users', function($table) {
            $table->string('token', 150)->nullable();;
        });
    }

    public function down()
    {
        $this->schema->table('users', function($table) {
            $table->dropColumn('token');
        });
    }
}
