<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public $table = 'Position';
    public $primaryKey = 'PosCode';
    protected $fillable = ['PosCode', 'Description', 'sg_no', 'position_short_name'];

    public function getPositionNameAttribute($value)
    {
        return Str::upper($value);
    }

}
