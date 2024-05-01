<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends EloquentUser
{
	protected $table = 'users';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'permissions',
		'level'
    ];

    /**
     * @return HasMany
     */
    public function email(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    // /**
    //  * @return HasMany
    //  */
    // public function activations(): HasMany
    // {
    //     return $this->hasMany(Activations::class);
    // }
}
