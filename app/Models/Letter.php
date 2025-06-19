<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\LetterFormat;
use App\Models\Org\Employee;

class Letter extends Model
{
    use SoftDeletes;

    protected $table = 'tb_letter';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'id_user',
        'id_letter_format',
        'subject',
        'body'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }

    public function format()
    {
        return $this->belongsTo(LetterFormat::class, 'id_letter_format');
    }
    public function user()
    {
        return $this->belongsTo(Employee::class, 'id_user'); // pastikan nama model Employee sesuai
    }
}
