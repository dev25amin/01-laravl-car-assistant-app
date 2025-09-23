<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_info_id',
        'analysis_type',
        'input_data',
        'analysis_result',
        'analysis_date',
        'analysis_images'
    ];

    protected $casts = [
        'input_data' => 'array',
        'analysis_date' => 'datetime',
        'analysis_images' => 'array'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع معلومات السيارة
     */
    public function carInfo()
    {
        return $this->belongsTo(CarInfo::class);
    }
}