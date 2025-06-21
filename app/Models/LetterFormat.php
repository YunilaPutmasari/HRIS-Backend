<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LetterFormat extends Model
{
    use SoftDeletes;

    protected $table = 'tb_letter_format';
    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'template',
        'type'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }
}
