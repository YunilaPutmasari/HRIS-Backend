<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\Models\Org\Employee;
use App\Models\Org\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{

    protected $table = 'tb_position';
    protected $primaryKey = 'id';
    public $incrementing = false;

    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id_department',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_position');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department');
    }
}
