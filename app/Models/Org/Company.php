<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\DailyUsageRecord;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Models\Org\Department;
use App\Models\Payment\Invoice; //tambahan untuk invoice

use App\Models\Attendance\CheckClock;
use App\Models\Attendance\CheckClockSetting;

class Company extends Model
{

    protected $table = "tb_company";
    protected $primaryKey = 'id';
    public $incrementing = false;

    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'id_manager',
        'id_subscription' //untuk nyantol ke subscription
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'effective_date',
        'created_at',
        'updated_at',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'id_manager');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'id_company');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'id_workplace');
    }

    public function employees()
    {
        // Melalui user ke employee
        return $this->hasManyThrough(Employee::class, User::class, 'id_workplace', 'id_user', 'id', 'id');
    }
    
    public function checkClocks()
    {
        return $this->hasManyThrough(CheckClock::class, User::class, 'id_workplace', 'id_user', 'id', 'id');
    }

    // Company yang harusnya punya subscription dan invoice
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'id_company');
    }

    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class, 'id_company')
            ->orderByDesc('starts_at');
    }
    // Belum selesai penambahan model dan controller
    public function dailyUsageRecords()
    {
        return $this->hasMany(DailyUsageRecords::class, 'id_company');
    }
    // Invoice harusnya dimiliki oleh company, ganti dari yg sebelumnya ada di user
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_subscription');
    }
}
