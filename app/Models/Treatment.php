<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', 'pet_id', 'doctor_id', 'diagnosis',
        'treatment_details', 'medications', 'instructions', 'follow_up_date'
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}