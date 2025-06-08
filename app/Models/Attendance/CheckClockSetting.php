<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class CheckClockSetting extends Model
{
    protected $table = "tb_check_clock_setting";
    protected $primaryKey = 'id';

    protected $with = [
        'checkClockSettingTime',
    ];

    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'id_company',
        'type'
    ];

    public function checkClockSettingTime()
    {
        return $this->hasMany(CheckClockSettingTime::class, 'id_ck_setting');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
}
