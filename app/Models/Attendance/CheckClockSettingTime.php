<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class CheckClockSettingTime extends Model
{
    protected $table = "tb_check_clock_setting_time";
    protected $primaryKey = 'id';

    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id_ck_setting',
        'day',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
    ];

    public function checkClockSetting()
    {
        return $this->belongsTo(CheckClockSetting::class, 'id');
    }
}
