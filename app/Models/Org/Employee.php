<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Org\Document;
use App\Models\Org\Position;
use Str;

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
        'avatar',
        'id_user',
        'first_name',
        'last_name',
        'nik',
        'address',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pendidikan',
        'email',
        'no_telp',
        'id_position',
        'start_date',
        'end_date',
        'tenure',
        'jadwal',
        'tipe_kontrak',
        'cabang',
        'employment_status',
        'tanggal_efektif',
        'bank',
        'norek',
        'gaji',
        'uang_lembur',
        'denda_terlambat',
        'total_gaji',
        'dokumen',

        'id_position',  // tambah ini
        'sign_in_code',
        'id_position',
        'employment_status',
        'tipeKontrak',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        // 'sign_in_code', //untuk ditampilkan di Profile
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
        return $this->belongsTo(Position::class, 'id_position', 'id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_user', 'id_user');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }


    /**
     * Override fungsi boot untuk otomatis generate UUID saat user dibuat.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
