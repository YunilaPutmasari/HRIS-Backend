<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Subscription\PackageType;
use App\Models\Subscription\Subscription;

class SubscriptionPendingChange extends Model
{   
    use HasFactory, HasUuids;

    protected $table = 'tb_pending_change';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_subscription',
        'id_new_package_type',
        'new_seats',
        'change_type', // upgrade/downgrade
        'reason_rejected',
        'status', // pending/approved/rejected/cancelled
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'id_subscription');
    }

    public function newPackageType()
    {
        return $this->belongsTo(PackageType::class, 'id_new_package_type');
    }
}
