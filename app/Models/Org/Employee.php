<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    protected $table = 'tb_employee';
    protected $primaryKey = 'id';
    public $incrementing = false;

    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'id_position',  // tambah ini
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id_position',
        'sign_in_code',
        'id_user',
        'created_at',
        'updated_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }

}
