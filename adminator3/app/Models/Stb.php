<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stb extends Model
{
	protected $table = 'objekty_stb';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_stb';

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
        'id_cloveka' => 0,
		'mac_adresa' => NULL,
		'puk' => NULL,
		'ip_adresa' => NULL,
        // TODO: add the of columns
    ];
}
