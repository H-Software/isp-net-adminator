<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * User
 *
 * @author    Haven Shen <havenshen@gmail.com>
 * @copyright Copyright (c) Haven Shen
 */
class PageLevel extends Model
{
    protected $table = 'leveling';

    public $level;

    public $desc;

    protected $fillable = [
    'level',
    'desc',
    ];
}
