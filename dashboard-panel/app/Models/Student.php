<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model {
    protected $fillable = ['name', 'bus_id', 'parent_name', 'parent_phone', 'home_latitude', 'home_longitude', 'attendance_status'];

    public function bus(): BelongsTo {
        return $this->belongsTo(Bus::class);
    }
}