<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivZmen extends Model
{
    protected $table = 'archiv_zmen';
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
    'akce' => null,
    // 'provedeno_kdy' => 'CURRENT_TIMESTAMP',
    'provedeno_kym' => null,
        'vysledek' => 0
    ];

     /**
      * The attributes that are mass assignable.
      *
      * @var array
      */
    protected $fillable = [
        'akce',
        'provedeno_kym',
        'vysledek' 
    ];
}
