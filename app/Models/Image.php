<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Image
 *
 * @property int         $id
 * @property int         $user_id   Владелец файла
 * @property string      $name      Исходный файл
 * @property string      $extension Расширение файла
 * @property int         $size      Размер файла
 * @property int         $pending   Ожидает обработки
 * @property string|null $description
 * @property int         $width     Ширина изображения
 * @property int         $height    Высота изображения
 * @property int         $visible   Видимость на сайте
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User   $user
 * @method static ImageFactory factory(...$parameters)
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image notUnique(string $fileName)
 * @method static Builder|Image query()
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereDeletedAt($value)
 * @method static Builder|Image whereDescription($value)
 * @method static Builder|Image whereExtension($value)
 * @method static Builder|Image whereHeight($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereName($value)
 * @method static Builder|Image wherePending($value)
 * @method static Builder|Image whereSize($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @method static Builder|Image whereUserId($value)
 * @method static Builder|Image whereVisible($value)
 * @method static Builder|Image whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|Image onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Image withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Image withoutTrashed()
 * @mixin Eloquent
 */
class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'extension',
        'size',
        'description',
        'width',
        'height',
        'visible',
    ];

    protected $casts = [
        'pending' => 'boolean',
        'visible' => 'boolean',
    ];

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
