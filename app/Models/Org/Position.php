<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
    protected $keyType = 'string';

    use HasFactory, Notifiable, SoftDeletes, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'gaji',
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
