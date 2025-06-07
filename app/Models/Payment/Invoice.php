<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Payment\Payment;
use App\Models\Subscription\Subscription;
use App\Models\Org\User;

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
        'id_user',
        'id_subscription', //untuk koneksi dengan subscription
        'total_amount',
        'due_datetime',
        'xendit_invoice_id', 
        'invoice_url',
    ];

    // Hidden
    /**
     * @var list<string>
     */
    protected $hidden = [
        'id_user',
        'created_at',
        'updated_at',
    ];

    /**
     * @var list<string>
     */
    protected $casts = [
        'total_amount' => 'double',
        'due_datetime' => 'datetime',
    ];

    // Relation
    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    // Invoice Relasi ke payment untuk semua pembayaran terkait
    public function payments(){
        return $this->hasMany(Payment::class, 'id_invoice');
    }

    public function subscription(){
        return $this->belongsTo(Invoice::class, 'id_subscription');
    }
    
}
