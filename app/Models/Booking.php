<?php

namespace App\Models;

use App\Traits\UUID;
use Database\Factories\BookingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Booking
 *
 * @property string $id
 * @property string $property_id
 * @property string $cleaner_id
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property string|null $secondary_contact
 * @property string|null $additional_information
 * @property string|null $cleaner_remarks
 * @property Carbon|null $rejected_at
 * @property Carbon|null $complete_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Services> $services
 * @property-read int|null $services_count
 * @method static BookingFactory factory($count = null, $state = [])
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereAdditionalInformation($value)
 * @method static Builder|Booking whereCleanerId($value)
 * @method static Builder|Booking whereCleanerRemarks($value)
 * @method static Builder|Booking whereCompleteAt($value)
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereEndTime($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking wherePropertyId($value)
 * @method static Builder|Booking whereRejectedAt($value)
 * @method static Builder|Booking whereSecondaryContact($value)
 * @method static Builder|Booking whereStartTime($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Booking extends Model
{
    use UUID, HasFactory;

    public $timestamps = true;
    public $incrementing = false;

    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'cleaner_id',
        'start_time',
        'end_time',
        'secondary_contact',
        'additional_information',
        'cleaner_remarks',
        'rejected_at',
        'complete_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['property_id', 'cleaner_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_time' => 'datetime',
        'rejected_at' => 'datetime',
        'complete_at' => 'datetime'
    ];

    /**
     * Get the booking's services.
     *
     * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Services::class);
    }
}
