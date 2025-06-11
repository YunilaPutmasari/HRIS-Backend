<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Org\User;

class CheckClock extends Model
{
    protected $table = "tb_check_clock";
    protected $primaryKey = 'id';

    protected $with = [
        'user',
        'checkClockSettingTime',
        'checkClockSetting',
    ];

    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id_user',
        'id_ck_setting',
        'id_ck_setting_time',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function checkClockSetting()
    {
        return $this->belongsTo(CheckClockSetting::class, 'id_ck_setting');
    }

    public function checkClockSettingTime()
    {
        return $this->belongsTo(CheckClockSettingTime::class, 'id_ck_setting_time');
    }
}
