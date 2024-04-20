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
		'akce' => NULL,
		'provedeno_kdy' => NULL,
		'provedeno_kym' => NULL,
        'vysledek' => 0
    ];
}
