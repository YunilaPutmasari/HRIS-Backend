<?php

namespace App\Models\Overtime;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeSettingRule extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'tb_overtime_setting_rule';

    protected $fillable = [
        'id_overtime_setting',
        'day_type',
        'start_hour',
        'end_hour',
        'rate_multiplier',
        'max_hour',
        'notes',
    ];

    protected $casts = [
        'rate_multiplier' => 'float',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(OvertimeSetting::class, 'id_overtime_setting', 'id');
    }
}
