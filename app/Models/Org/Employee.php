<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Org\Document;

class Employee extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tb_employee';
    protected $primaryKey = 'id';


    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = [
        'id_user',
        'avatar',
        'first_name',
        'last_name',
        'nik',
        'address',
        'notelp',
        'email',
        'tempatLahir',
        'tanggalLahir',
        'jenisKelamin',
        'pendidikan',
        'jadwal',
        'tipeKontrak',
        'grade',
        'jabatan',
        'cabang',
        'bank',
        'norek',
        'dokumen',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id_position',
        'created_at',
        'updated_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'id_user');
    // }
    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_user', 'id_user');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }


}
