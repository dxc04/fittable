<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAnalysis extends Model
{
    protected $fillable = [
        'job_posting_id',
        'company_background',
        'location',
        'job_type',
        'summary',
        'required_skills',
        'nice_to_have_skills',
        'responsibilities',
        'requirements',
        'benefits',
        'salary_range',
        'hiring_process',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'nice_to_have_skills' => 'array',
            'responsibilities' => 'array',
            'requirements' => 'array',
            'benefits' => 'array',
        ];
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
