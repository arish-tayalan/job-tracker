<?php

namespace App\Models;

use App\Enums\InterviewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    /** @use HasFactory<\Database\Factories\InterviewFactory> */
    use HasFactory;

    /**
     * job_application_id is left out on purpose: it is set by creating
     * through the relationship ($application->interviews()->create(...)).
     *
     * @var list<string>
     */
    protected $fillable = [
        'scheduled_at',
        'type',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'type' => InterviewType::class,
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
