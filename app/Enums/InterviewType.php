<?php

namespace App\Enums;

/** The kind of interview round. Stored as the string value. */
enum InterviewType: string
{
    case Phone = 'phone';
    case Technical = 'technical';
    case Hr = 'hr';
    case Onsite = 'onsite';
    case Final = 'final';

    public function label(): string
    {
        return match ($this) {
            self::Hr => 'HR',
            default => ucfirst($this->value),
        };
    }
}
