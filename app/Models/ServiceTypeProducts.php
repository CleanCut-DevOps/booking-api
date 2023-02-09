<?php

namespace App\Models;

use App\Traits\UUID;
use Database\Factories\ServiceTypeProductsFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ServiceTypeProducts
 *
 * @property string $id
 * @property string $service_type_id
 * @property string $label
 * @property int|null $quantity
 * @property-read ServiceType $serviceType
 * @method static ServiceTypeProductsFactory factory($count = null, $state = [])
 * @method static Builder|ServiceTypeProducts newModelQuery()
 * @method static Builder|ServiceTypeProducts newQuery()
 * @method static Builder|ServiceTypeProducts query()
 * @method static Builder|ServiceTypeProducts whereId($value)
 * @method static Builder|ServiceTypeProducts whereLabel($value)
 * @method static Builder|ServiceTypeProducts whereQuantity($value)
 * @method static Builder|ServiceTypeProducts whereServiceTypeId($value)
 * @mixin Eloquent
 */
class ServiceTypeProducts extends Model
{
    use UUID, HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'service_type_products';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'quantity',
        'service_type_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['id', 'service_type_id'];

    /**
     * Get the product's service type.
     *
     * @return BelongsTo
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
