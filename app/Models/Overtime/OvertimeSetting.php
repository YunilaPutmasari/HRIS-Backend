<?php

namespace App\Models\Overtime;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeSetting extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $table = 'tb_overtime_setting';

    protected $fillable = [
        'name',
        'source',
        'is_active',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(OvertimeSettingRule::class, 'overtime_setting_id', 'id');
    }
}
