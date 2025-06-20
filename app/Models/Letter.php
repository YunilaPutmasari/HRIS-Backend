<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\LetterFormat;
// use App\Models\Org\Employee;
use App\Models\Org\User;



class Letter extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id_letter_format' => 'string',
    ];

    protected $table = 'tb_letter';
    public $incrementing = false;
    protected $keyType = 'string';


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
    public function user() // ✅ perbaiki di sini
    {
        return $this->belongsTo(User::class, 'id_user'); // ambil dari tb_user
    }
}
