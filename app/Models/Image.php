<?php

namespace App\Models;

use App\Casts\DescriptionCast;
use App\Casts\ImagePaletteCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'extension',
        'size',
        'description',
        'width',
        'height',
    ];

    protected $casts = [
        'description' => DescriptionCast::class,
        'palette'     => ImagePaletteCast::class,
        'processed'   => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверяет изображение на уникальность.
     * Вернёт "true", если указанный md5-хэш файла уже есть в БД.
     *
     * @param  Builder  $query
     * @param  string   $fileName
     *
     * @return bool
     */
    public function scopeNotUnique(Builder $query, string $fileName): bool
    {
        return $query->where('name', $fileName)->exists();
    }

}
