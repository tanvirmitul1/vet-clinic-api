<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', 'owner_id', 'invoice_number', 'subtotal',
        'tax_amount', 'total_amount', 'status', 'due_date',
        'paid_date', 'payment_method', 'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}