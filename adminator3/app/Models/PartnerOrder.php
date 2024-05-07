<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Core\adminator;

class PartnerOrder extends Model
{
    protected $table = 'partner_klienti';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        // 'akce' => NULL,
        // // 'provedeno_kdy' => 'CURRENT_TIMESTAMP',
        // 'provedeno_kym' => NULL,
        // 'vysledek' => 0
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jmeno',
        'adresa',
        'email',
        'tel',
        'poznamky',
        'typ_balicku',
        'typ_linky',
        'vlozil'
    ];


    /**
     * present integer like a text value
     */
    protected function akceptovano(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => is_null($value) ? null : adminator::convertIntToBoolTextCs($value),
        );
    }

    /**
     * present integer like a text value
     */
    protected function pripojeno(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => adminator::convertIntToBoolTextCs($value),
        );
    }

    /**
     * present integer like a text value
     */
    protected function prio(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => adminator::convertIntToTextPrioCs($value),
        );
    }
}
