<?php

namespace App\Enums;

enum ProjectPriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';

    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-100 text-slate-800',
            self::Medium => 'bg-amber-100 text-amber-800',
            self::High => 'bg-red-100 text-red-800',
        };
    }
}
