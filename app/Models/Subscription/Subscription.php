<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Org\Company;
use App\Models\Subscription\PackageType;
use App\Models\Subscription\SubscriptionPendingChange;
use App\Models\Payment\Invoice;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'tb_subscription';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [ //Sementara karena sek tidak aman price & active di fillable
        'id_company',
        // 'package_type',
        'id_package_type', //perlu ada tabel baru untuk package
        'seats',
        'price_per_seat',
        'is_trial',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'status', //active, expired, pending_upgrade, pending_downgrade, cancelled
        'is_cancelled',
        'id_pending_change', //untuk tabel pending change jika nanti jadi aku tambahkan
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'price_per_seat' => 'float',
        'is_trial' => 'boolean',
        'is_cancelled' => 'boolean',
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
    // Pindah bawah agar mudah tracking invoice, subscription dan change
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_subscription');
    }
    
    public function packageType()
    {
        return $this->belongsTo(PackageType::class,'id_package_type');
    }

    public function pendingChange()
    {
        return $this->hasOne(SubscriptionPendingChange::class,'id_subscription');
    }
    // cek true false pakai function bool
    public function isFreePlan(): bool
    {
        return $this->packageType && $this->packageType->is_free;
    }

    public function isPendingChange(): bool{
        return in_array($this->status, ['pending_upgrade','pending_downgrade']);
    }

    // function donwgrade dan upgrade
    public function isDowngradable(Company $company): array
    {
        $totalEmployees = $company->employees()->count();

        if ($this->packageType->max_seats < $totalEmployees) {
            return [false, "Jumlah karyawan melebihi batas Seat Maksimal Paket"];
        }

        if ($this->seats < $totalEmployees) {
            return [false, "Jumlah seat kurang dari jumlah karyawan"];
        }

        return [true, ""];
    }
}

