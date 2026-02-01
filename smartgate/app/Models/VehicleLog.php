<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleLog extends Model
{
    protected $fillable = [
        'vehicle_registration_id',
        'rfid_tag_id',
        'type',
        'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime'
    ];

    public function vehicleRegistration(): BelongsTo
    {
        return $this->belongsTo(VehicleRegistration::class, 'vehicle_registration_id');
    }
}
