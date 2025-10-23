<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'company',
        'original_text',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobAnalysis(): HasOne
    {
        return $this->hasOne(JobAnalysis::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function close(): void
    {
        $this->update(['closed_at' => now()]);

        // Also close all related assessments
        $this->assessments()->whereNull('closed_at')->update(['closed_at' => now()]);
    }

    public function reopen(): void
    {
        $this->update(['closed_at' => null]);

        // Note: We don't automatically reopen assessments as they might have been individually closed
    }
}
