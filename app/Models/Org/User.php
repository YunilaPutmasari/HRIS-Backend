<?php

namespace App\Models\Org;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Str;

class User extends Authenticatable
{
    protected $table = 'tb_user';

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $with = [
        'employee',
        'workplace'
    ];


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'phone_number',
        'password',
        'is_admin',
        'id_workplace' // TAK TAMBAHKAN INI UNTUK NANTI HRD MENAMBAHKAN EMPLOYEE
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setIsAdminAttribute($value)
    {
        $this->attributes['is_admin'] = $value ? '1' : '0';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    // INI JIKA MANAGER BISA LEBIH DARI 1 COMPANY
    public function companies()
    {
        return $this->hasMany(Company::class, 'id_manager');
    }

    // INI JIKA INGIN DIGANTI DARI WORKPLACE MENJADI COMPANY BIAR LEBIH MUDAH
    public function workplace()
    {
        return $this->belongsTo(Company::class, 'id_workplace');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id_user');
    }

    public function personal_access_tokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id');
    }
    // User.php
    public function dokumen()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    public function isManagerOf(Company $company): bool
    {
        return $this->id === $company->id_manager && $this->isAdmin();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Override fungsi boot untuk otomatis generate UUID saat user dibuat.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
