<?php

namespace App\Models;

use App\Traits\UUID;
use Database\Factories\TypeCategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\TypeCategory
 *
 * @property string $id
 * @property string $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ServiceType> $serviceTypes
 * @property-read int|null $service_types_count
 * @method static TypeCategoryFactory factory($count = null, $state = [])
 * @method static Builder|TypeCategory newModelQuery()
 * @method static Builder|TypeCategory newQuery()
 * @method static Builder|TypeCategory query()
 * @method static Builder|TypeCategory whereCreatedAt($value)
 * @method static Builder|TypeCategory whereId($value)
 * @method static Builder|TypeCategory whereLabel($value)
 * @method static Builder|TypeCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TypeCategory extends Model
{
    use UUID, HasFactory;

    public $timestamps = true;
    public $incrementing = false;

    protected $table = 'service_type_categories';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the category's service types.
     *
     * @return HasMany
     */
    public function serviceTypes(): HasMany
    {
        return $this->hasMany(ServiceType::class);
    }
}
