<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakturacniSkupina extends Model
{
    protected $table = 'fakturacni_skupiny';
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
        'typ' => 0,
        'typ_sluzby' => 0,
        'fakturacni_text' => null,
        'vlozil_kdo' => null,
        'sluzba_int' => 0,
        'sluzba_int_id_tarifu' => 0,
        'sluzba_iptv' => 0,
        'sluzba_iptv_id_tarifu' => 0,
        'sluzba_voip' => 0,
        'sluzba_voip_id_tarifu' => 0,
    ];

}
