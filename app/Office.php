<?php

namespace App;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'Office';
    public $primaryKey = 'OfficeCode';
    public $increment = false;
    protected $keyType = 'string';

}
