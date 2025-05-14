<?php

namespace App\Plugins\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\User;
use App\Plugins\Discount\Models\Discount;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'tenant_id', // removed - doesn't exist in database
        'reservation_number',
        'hotel_id',
        'room_id',
        'room_type_id', // Migration dosyasında yok ama kod çağrıyor
        'check_in',
        'check_out',
        'nights',
        'adults',
        'children',
        'total_price',
        'original_price',
        'discount_amount',
        'discount_code',
        'has_discount',
        'discount_id',
        'currency',
        'status',
        'notes',
        'payment_status',
        'payment_method',
        'source',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'nights' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'total_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'has_discount' => 'boolean',
        // Gereksiz veya veritabanında olmayan alanlar kaldırıldı
    ];

    /**
     * Modelin başlatılması sırasında olay dinleyicileri
     */
    protected static function boot()
    {
        parent::boot();
        
        // Oluşturma ve güncelleme olayları için kullanıcı ID'lerini ayarla
        static::creating(function ($model) {
            // Unique reservation number üret
            if (!$model->reservation_number) {
                $model->reservation_number = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
            
            // Gecelik sayısını otomatik hesapla
            if ($model->check_in && $model->check_out) {
                $model->nights = $model->check_in->diffInDays($model->check_out);
            }
            
            // amount_paid ve balance_due kaldırıldı - veritabanında yok
        });
        
        static::updating(function ($model) {
            // Gecelik sayısını otomatik güncelle
            if ($model->isDirty(['check_in', 'check_out'])) {
                $model->nights = $model->check_in->diffInDays($model->check_out);
            }
            
            // amount_paid ve balance_due kaldırıldı - veritabanında yok
        });
    }

    /**
     * Otel ilişkisi
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Oda ilişkisi
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Misafirler ilişkisi
     */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Oluşturan kullanıcı ilişkisi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Güncelleyen kullanıcı ilişkisi
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Primary misafir
     */
    public function primaryGuest()
    {
        return $this->guests()->where('is_primary', true)->first();
    }

    /**
     * Get the applied discount
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the discount usages for this reservation
     */
    public function discountUsages()
    {
        return $this->morphMany(\App\Plugins\Discount\Models\DiscountUsage::class, 'order');
    }
}