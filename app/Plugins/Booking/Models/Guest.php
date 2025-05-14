<?php

namespace App\Plugins\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'tenant_id', // Removed as it doesn't exist in database
        // 'created_by',
        // 'updated_by',
        'reservation_id',
        'first_name',
        'last_name',
        'birth_date',
        'id_number',
        'id_type',
        'nationality',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'notes',
        'is_primary',
        'guest_type',
        'age',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_primary' => 'boolean',
        'age' => 'integer',
    ];

    /**
     * Misafirin tam adını döndürür
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Rezervasyon ilişkisi
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}