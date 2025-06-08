<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CheckClock extends Model
{
    protected $table = "tb_check_clock";
    protected $primaryKey = 'id';

    protected $with = [
        'checkClockSetting',
    ];

    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id_user',
        'id_ck_setting',
        'clock_in',
        'clock_out',
        'status',
    ];

    public function checkClockSetting()
    {
        return $this->belongsTo(CheckClockSetting::class, 'id_ck_setting');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
