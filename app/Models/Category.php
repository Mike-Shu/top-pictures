<?php

namespace App\Models;

use App\Casts\CategoryColorsCast;
use App\Casts\DescriptionCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'colors',
        'cover_image_id',
    ];

    protected $casts = [
        'description' => DescriptionCast::class,
        'colors'      => CategoryColorsCast::class,
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Проверяет категорию на уникальность.
     * Вернёт "true", если указанное имя уже есть в БД.
     *
     * @param  Builder  $query
     * @param  string   $name
     *
     * @return bool
     */
    public function scopeNotUnique(Builder $query, string $name): bool
    {
        return $query->where('name', $name)->exists();
    }

}
