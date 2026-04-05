<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_registration_id',
        'admin_id',
        'action',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the vehicle registration being reviewed
     */
    public function vehicleRegistration(): BelongsTo
    {
        return $this->belongsTo(VehicleRegistration::class, 'vehicle_registration_id');
    }

    /**
     * Get the admin who reviewed this registration
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
