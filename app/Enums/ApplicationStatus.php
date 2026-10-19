<?php

namespace App\Enums;

/**
 * The stages a job application can be in.
 * Stored in the database as the string value (e.g. "interviewing").
 */
enum ApplicationStatus: string
{
    case Wishlist = 'wishlist';
    case Applied = 'applied';
    case Interviewing = 'interviewing';
    case Offer = 'offer';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** Tailwind classes for the status badge. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Wishlist => 'bg-gray-100 text-gray-700',
            self::Applied => 'bg-blue-100 text-blue-700',
            self::Interviewing => 'bg-amber-100 text-amber-800',
            self::Offer => 'bg-green-100 text-green-700',
            self::Rejected => 'bg-red-100 text-red-700',
            self::Withdrawn => 'bg-slate-200 text-slate-600',
        };
    }
}
