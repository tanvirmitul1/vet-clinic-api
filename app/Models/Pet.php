<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'species', 'breed', 'color', 'birth_date', 
        'gender', 'weight', 'medical_history', 'notes', 
        'owner_id', 'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) return null;
        return $this->birth_date->diffInYears(now());
    }
}