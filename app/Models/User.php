<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // علاقة المستخدم بالمنتجات
    public function products()
    {
        return $this->hasMany(Product::class);
    }

public function carInfo()
{
    return $this->hasOne(CarInfo::class);
}


        public function carAnalysis()
    {
        return $this->hasMany(CarAnalysis::class);
    }

    
}
