<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $table = "tb_employee_request";

    protected $fillable = [
        'id_user',
        'request_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
    ];
}
