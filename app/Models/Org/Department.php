<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{

    protected $table = 'tb_department';
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
        'location',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class, 'id_department');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'id_company');
    }
}
