<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'doctor_id', 'appointment_date', 'status', 
        'reason', 'notes', 'estimated_cost'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function treatment()
    {
        return $this->hasOne(Treatment::class);
    }

    public function billing()
    {
        return $this->hasOne(Billing::class);
    }
}