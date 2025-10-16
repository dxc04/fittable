<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'user_id',
        'resume_id',
        'job_posting_id',
        'overall_match',
        'skill_breakdown',
        'summary',
        'strengths',
        'gaps',
        'application_strategy',
        'interview_preparation',
        'personalized_recommendation',
    ];

    protected function casts(): array
    {
        return [
            'skill_breakdown' => 'array',
            'strengths' => 'array',
            'gaps' => 'array',
            'application_strategy' => 'array',
            'interview_preparation' => 'array',
            'personalized_recommendation' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
