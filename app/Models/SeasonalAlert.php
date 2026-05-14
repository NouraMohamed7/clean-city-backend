<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonalAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'month',
        'event_name',
        'predicted_increase_percent',
        'recommendation',
        'based_on_years',
        'is_active',
    ];

    protected $casts = [
        'month' => 'integer',
        'predicted_increase_percent' => 'integer',
        'based_on_years' => 'array',
        'is_active' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}