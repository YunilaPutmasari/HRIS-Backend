<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscription\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PackageType extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'tb_package_types';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable=[
        'name',
        'description',
        'max_seats',
        'price_per_seat',
        'is_free', //bakalan set true jika free plan
    ];

    protected $casts = [
        'price_per_seat' => 'float',
        'is_free' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
