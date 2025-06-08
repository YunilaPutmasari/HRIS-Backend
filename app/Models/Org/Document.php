<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Org\Employee;
use App\Models\Org\User;

class Document extends Model
{
    use HasFactory;

    protected $table = 'tb_documents';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_user',
        'type',
        'name',
        'file_path'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_user', 'id_user');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
