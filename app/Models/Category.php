<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'amount',
        'colors',
    ];

    protected $casts = [
        'colors' => 'array',
    ];

    /**
     * @return HasMany
     */
    public function image(): HasMany
    {
        return $this->hasMany(Image::class);
    }

}
