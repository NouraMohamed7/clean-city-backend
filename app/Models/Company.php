<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'city_id',
        'coverage_areas',
        'phone',
        'email',
        'rating_average',
        'total_resolved',
        'is_active',
    ];

    protected $casts = [
        'rating_average' => 'decimal:1',
        'total_resolved' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'assigned_company_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function scheduledCleanups(): HasMany
    {
        return $this->hasMany(ScheduledCleanup::class);
    }
}