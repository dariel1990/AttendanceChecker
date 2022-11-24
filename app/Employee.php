<?php

namespace App;

use App\Office;
use App\Office2;
use App\Position;
use App\OfficeCharging;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'Employee_id';
    protected $table = 'Employees';
    public $with = ['position', 'office_charging', 'office_assignment', 'office'];
    public $keyType = 'string';

    protected $fillable = [
        'Employee_id',
        'LastName',
        'FirstName',
        'MiddleName',
        'Suffix',
        'OfficeCode',
        'OfficeCode2',
        'PosCode',
        'Designation',
        'Gender',
        'CivilStatus',
        'Birthdate',
        'Address',
        'ContactNumber',
        'ImagePhoto',
        'isActive',
        'created_at',
        'updated_at',
    ];

    public const ACTIVE = 1;

    public const IN_ACTIVE = 0;

    public $appends = [
        'fullname'
    ];


    public function getFullnameAttribute()
    {
        $middleName = ($this->MiddleName == '') ? '' : substr($this->MiddleName, 0, 1) . ".";
        return "{$this->FirstName} " . $middleName . " {$this->LastName} {$this->Suffix}";
    }

    public function position()
    {
        return $this->hasOne(Position::class, 'PosCode', 'PosCode')->withDefault();
    }

    public function office()
    {
        return $this->hasOne(Office::class, 'OfficeCode', 'OfficeCode')->withDefault();
    }

    public function office_charging()
    {
        return $this->hasOne(OfficeCharging::class, 'OfficeCode', 'OfficeCode')->withDefault();
    }

    public function office_assignment()
    {
        return $this->hasOne(Office2::class, 'OfficeCode2', 'OfficeCode2')->withDefault();
    }

    public function scopeExclude($query, array $value = [])
    {
        return $query->select(array_diff($this->columns, $value));
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', self::ACTIVE);
    }

    public function scopePermanent($query)
    {
        return $query->where('Work_Status', 'not like', '%' . 'JOB ORDER' . '%')
            ->where('Work_Status', 'not like', '%' . 'CONTRACT OF SERVICE' . '%')
            ->where('Work_Status', '!=', '');
    }
}
