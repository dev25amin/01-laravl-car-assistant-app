<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_brand',
        'car_model',
        'car_year',
        'current_mileage',
        'last_oil_change',
        'last_maintenance',
        'fuel_level',
        'notes'
    ];

    protected $casts = [
        'last_oil_change' => 'date',
        'last_maintenance' => 'date',
        'fuel_level' => 'decimal:2',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع تحليلات السيارة
     */
    public function analyses()
    {
        return $this->hasMany(CarAnalysis::class);
    }
}