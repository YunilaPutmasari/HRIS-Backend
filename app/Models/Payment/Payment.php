<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    protected $table = 'tb_payment';
    protected $primaryKey = 'id';
    public $incrementing = false;

    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

     protected $fillable = [
        'id_invoice',
        'payment_code',
        'amount_paid',
        'currency',
        'payment_method',
        'status',
        'payment_datetime',
     ];

     // Hidden
     /**
      * @var list<string>
      */
     protected $hidden = [
        'id_invoice',
        'created_at',
        'updated_at',
     ];

     /**
      * @var list<string>
      */
     protected $casts = [
        'amount_paid' => 'double',
        'payment_datetime' => 'datetime',
     ];

     // Payment Relasi ke invoice terkait
     public function invoice(){
        return $this->belongsTo(Invoice::class, 'id_invoice');
     }
     
}
