<?php

namespace App\Models;

use App\Traits\UUID;
use Database\Factories\ServiceTypeFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\ServiceType
 *
 * @property string $id
 * @property string $type_category_id
 * @property string $label
 * @property float $price
 * @property bool $quantifiable
 * @property bool $available
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $category
 * @property-read Collection $products
 * @property-read Collection<int, ServiceTypeProducts> $serviceTypeProducts
 * @property-read int|null $service_type_products_count
 * @property-read TypeCategory $typeCategory
 * @method static ServiceTypeFactory factory($count = null, $state = [])
 * @method static Builder|ServiceType newModelQuery()
 * @method static Builder|ServiceType newQuery()
 * @method static Builder|ServiceType query()
 * @method static Builder|ServiceType whereAvailable($value)
 * @method static Builder|ServiceType whereCreatedAt($value)
 * @method static Builder|ServiceType whereId($value)
 * @method static Builder|ServiceType whereLabel($value)
 * @method static Builder|ServiceType wherePrice($value)
 * @method static Builder|ServiceType whereQuantifiable($value)
 * @method static Builder|ServiceType whereTypeCategoryId($value)
 * @method static Builder|ServiceType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ServiceType extends Model
{
    use UUID, HasFactory;

    public $appends = ['category', 'products'];

    public $timestamps = true;
    public $incrementing = false;

    protected $table = 'service_types';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'price',
        'available',
        'quantifiable',
        'type_category_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['type_category_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'quantifiable' => 'boolean'
    ];

    /**
     * Appends the service type's category.
     *
     * @return string | null
     */
    public function getCategoryAttribute(): string|null
    {
        return $this->typeCategory()->first()->label;
    }

    /**
     * Get the service type's category.
     *
     * @return BelongsTo
     */
    public function typeCategory(): BelongsTo
    {
        return $this->belongsTo(TypeCategory::class);
    }

    /**
     * Appends the service type's products.
     *
     * @return Collection
     */
    public function getProductsAttribute(): Collection
    {
        return $this->serviceTypeProducts()->get();
    }

    /**
     * Get the service type's products.
     *
     * @return HasMany
     */
    public function serviceTypeProducts(): HasMany
    {
        return $this->hasMany(ServiceTypeProducts::class);
    }
}
