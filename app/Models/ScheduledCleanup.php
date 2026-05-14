<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledCleanup extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'area_name',
        'description',
        'start_date',
        'frequency',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}