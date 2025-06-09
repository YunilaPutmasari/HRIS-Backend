<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id_manager',
        'id_subscription',
        'effective_date',
        'created_at',
        'updated_at',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'id_manager');
    }

    // public function employees()
    // {
    //     return $this->hasMany(User::class, 'id_company');
    // }

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

}
