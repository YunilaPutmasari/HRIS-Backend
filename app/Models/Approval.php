<?php

namespace App\Models;

use App\Models\Org\Employee;
use App\Models\Org\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approval extends Model
{
    use HasFactory, HasUuids;

    protected $table = "tb_employee_request";

    protected $fillable = [
        'id_user',
        'request_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'document',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function employee(){
        return $this->hasOne(Employee::class, 'id_user', 'id_user');
    }
}
