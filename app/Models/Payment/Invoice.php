<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Payment\Payment;
use App\Models\Subscription\Subscription;
use App\Models\Org\User;
use App\Models\Org\Company;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    protected $table = 'tb_invoice';
    protected $primaryKey = 'id';
    public $incrementing = false;

    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

     protected $fillable = [
        // 'id_user', //gajadi karena kita akan ganti ke company bukan user
        'id_company', //cek ketok foreign
        'id_subscription', //untuk koneksi dengan subscription
        'total_amount',
        'status', //kurang aman karena di fillable, sek aku blm paham mengamankan e
        'due_datetime',
        'payment_date', //tambahan untuk tanggal pembayaran
        'xendit_invoice_id', 
        'invoice_url',
    ];

    // Hidden
    /**
     * @var list<string>
     */
    protected $hidden = [
        // 'id_user',
        'company_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var list<string>
     */
    protected $casts = [
        'total_amount' => 'float',
        'due_datetime' => 'datetime',
        'status' => 'string',
    ];

    // Relation, ini ga dipake bakalan karena pakai company
    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'id_company');
    }

    // Invoice Relasi ke payment untuk semua pembayaran terkait
    public function payments(){
        return $this->hasMany(Payment::class, 'id_invoice');
    }

    public function subscription(){
        return $this->belongsTo(Subscription::class, 'id_subscription');
    }
    
}
