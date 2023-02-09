<?php

namespace App\Models;

use Database\Factories\ServicesFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Services
 *
 * @property string $booking_id
 * @property string $type_id
 * @property int $quantity
 * @property-read Booking $booking
 * @property-read Model|null $type
 * @property-read ServiceType $serviceType
 * @method static ServicesFactory factory($count = null, $state = [])
 * @method static Builder|Services newModelQuery()
 * @method static Builder|Services newQuery()
 * @method static Builder|Services query()
 * @method static Builder|Services whereBookingId($value)
 * @method static Builder|Services whereQuantity($value)
 * @method static Builder|Services whereTypeId($value)
 * @mixin Eloquent
 */
class Services extends Model
{
    use HasFactory;

    public $appends = ['type'];

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'services';
    protected $primaryKey = 'booking_id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_id',
        'quantity',
        'booking_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['booking_id', 'type_id'];

    /**
     * Get the service's booking.
     *
     * @return BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Appends the service's type.
     *
     * @return Model|null
     */
    public function getTypeAttribute(): Model|null
    {
        return $this->serviceType()->first();
    }

    /**
     * Get the service's type.
     *
     * @return BelongsTo
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
