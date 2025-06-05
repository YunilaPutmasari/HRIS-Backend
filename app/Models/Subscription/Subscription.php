<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Org\Company;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'tb_subscription';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [ //Sementara karena sek tidak aman price & active di fillable
        'id_company',
        'package_type',
        'seats',
        'price_per_seat',
        'is_trial',
        'trial_ends_at',
        'start_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'price_per_seat' => 'float',
        'is_trial' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function isActive(): bool
    {
        return !$this->trashed() && $this->status === 'active' && now()->lt($this->ends_at);
    }

    public function isInTrial(): bool
    {
        return $this->is_trial && now()->lt($this->trial_ends_at);
    }
}

