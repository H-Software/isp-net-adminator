<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $subject
 * @property int    $user_id
 * @property string $uri
 */
class UserEmail extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'subject',
        'user_id',
        'uri',
    ];

    /**
     * @var string
     */
    protected $table = 'users_email';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // /**
    //  * @return BelongsTo
    //  */
    // public function activations(): BelongsTo
    // {
    //     return $this->belongsTo(Activations::class, 'user_id', 'user_id');
    // }
}
