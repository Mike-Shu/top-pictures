<?php

namespace App\Models;

use App\Services\Category\Casts\ColorsCast;
use App\Services\Category\Casts\DescriptionCast;
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
        'amount',
        'colors',
    ];

    protected $casts = [
        'description' => DescriptionCast::class,
        'colors'      => ColorsCast::class,
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function image(): HasMany
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
