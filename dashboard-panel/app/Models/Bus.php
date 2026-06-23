<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model {
    protected $fillable = ['bus_number', 'driver_name', 'driver_phone', 'status'];

    public function students(): HasMany {
        return $this->hasMany(Student::class);
    }
}