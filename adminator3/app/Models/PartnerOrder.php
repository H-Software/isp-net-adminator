<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * @var array
     */
    protected $fillable = [
        // 'akce',
        // 'provedeno_kym',
        // 'vysledek' 
    ];
}
