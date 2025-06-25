<?php

namespace App\Models\Overtime;

use App\Models\Org\Employee;
use App\Models\Org\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'tb_overtime';

    protected $fillable = [
        'id_user',
        'overtime_date',
        'start_time',
        'end_time',
        'id_overtime_setting',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id_user', 'id_user');
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(OvertimeSetting::class, 'id_overtime_setting', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
