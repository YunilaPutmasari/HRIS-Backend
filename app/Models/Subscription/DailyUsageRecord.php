<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Models\Org\Company;
use App\Http\Models\Subscription\Subscription;

class DailyUsageRecord extends Model
{
    use HasFactory;
    protected $table  = 'tb_daily_usage_records';

    protected $fillable = [
        'id_company', //paka company_id cek terlihat kalau itu foreign atau references
        'id_subscription',
        'date',
        'daily_cost', //perhitungan daily cost pay-as-you-go
    ];

    protected $casts = [
        'date' => 'date',
        'daily_cost' => 'float',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'id_subscription');
    }
}
