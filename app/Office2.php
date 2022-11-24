<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office2 extends Model
{
    protected $primaryKey = 'OfficeCode2';
    protected $keyType = 'string';
    public $table = 'Office2';

    protected $fillable = [
        'OfficeCode2',
        'Description',
        'OfficeShort',
        'OfficeHead',
        'PositionName',
        'Display',
        'OfficeHead2',
        'PositionName2',
        'isHospital',
    ];
}
