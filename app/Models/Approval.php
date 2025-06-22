<?php

namespace App\Models;

use App\Models\Org\Employee;
use App\Models\Org\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = ['document_url'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function employee(){
        return $this->hasOne(Employee::class, 'id_user', 'id_user');
    }

    public function documentUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                !empty($attributes['document'])
                    ? Storage::disk('public')->url($attributes['document'])
                    : null,
        );
    }
}
