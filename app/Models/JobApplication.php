<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFactory> */
    use HasFactory;

    /**
     * Fields that can be filled from a form. user_id is deliberately left out
     * so a user can never assign an application to someone else.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company',
        'position',
        'status',
        'location',
        'job_url',
        'applied_on',
        'follow_up_on',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'applied_on' => 'date',
            'follow_up_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** One application has many interviews, soonest first. */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class)->orderBy('scheduled_at');
    }

    /** True when the follow-up date has arrived and the application is still open. */
    public function followUpIsDue(): bool
    {
        return $this->follow_up_on !== null
            && $this->follow_up_on->lte(today())
            && in_array($this->status, [ApplicationStatus::Applied, ApplicationStatus::Interviewing], true);
    }
}
