<?php

declare(strict_types=1);

namespace App\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

use App\Models;

/**
 * Class Migration.
 */
class Migration extends AbstractMigration
{
    /**
     * @var \Illuminate\Database\Capsule\Manager $capsule 
     */
    public $capsule;

    /**
     * @var Builder
     */
    protected Builder $schema;

    public function init(): void
    {
        $this->schema = Capsule::schema();
    }
}
