<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'Planning';
    case InProgress = 'In Progress';
    case OnHold = 'On Hold';
    case Completed = 'Completed';

    public function badgeClass(): string
    {
        return match ($this) {
            self::Planning => 'bg-blue-100 text-blue-800',
            self::InProgress => 'bg-yellow-100 text-yellow-800',
            self::OnHold => 'bg-gray-100 text-gray-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
