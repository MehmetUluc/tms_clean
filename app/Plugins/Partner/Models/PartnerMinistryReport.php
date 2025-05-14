<?php

namespace App\Plugins\Partner\Models;

use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PartnerMinistryReport extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_ministry_reports';

    protected $fillable = [
        'partner_id',
        'hotel_id',
        'report_date',
        'report_type',
        'file_path',
        'status',
        'notes',
        'report_data',
        'submitted_at',
        'processed_at',
        'submitted_by',
        'submission_reference',
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'json',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the partner that owns the ministry report.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the hotel associated with the ministry report.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the user who submitted the report.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the URL to the report file.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Check if the report is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the report is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if the report is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the report is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Mark the report as submitted.
     */
    public function markAsSubmitted(int $submittedBy, string $submissionReference = null, string $notes = null): bool
    {
        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->submitted_by = $submittedBy;
        
        if ($submissionReference) {
            $this->submission_reference = $submissionReference;
        }
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Mark the report as approved.
     */
    public function markAsApproved(string $notes = null): bool
    {
        $this->status = 'approved';
        $this->processed_at = now();
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Mark the report as rejected.
     */
    public function markAsRejected(string $notes = null): bool
    {
        $this->status = 'rejected';
        $this->processed_at = now();
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Generate a unique submission reference.
     */
    public static function generateSubmissionReference(): string
    {
        return 'RPT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Scope a query to only include reports with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include reports of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope a query to include reports within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include reports for a specific hotel.
     */
    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }
}