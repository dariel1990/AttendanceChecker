<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeCharging extends Model
{
    protected $primaryKey = 'office_code';
    protected $keyType = 'string';
    public $table = 'Office';
}
