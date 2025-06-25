<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Org\Company;
use App\Models\Org\User;


class CheckClockSetting extends Model
{
    protected $table = "tb_check_clock_setting";
    protected $primaryKey = 'id';

    protected $with = [
        'checkClockSettingTime',
        'users',
    ];

    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'id_company',
        'type',
        'location_lat',
        'location_lng',
        'radius',
    ];

    public function checkClockSettingTime()
    {
        return $this->hasMany(CheckClockSettingTime::class, 'id_ck_setting');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_check_clock_setting');
    }
}
