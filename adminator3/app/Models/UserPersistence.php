<?php

namespace App\Models;

use Cartalyst\Sentinel\Persistences\EloquentPersistence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPersistence extends EloquentPersistence
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_persistences';

    /**
     * The Users model FQCN.
     *
     * @var string
     */
    protected static $usersModel = User::class;

}
