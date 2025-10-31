<?php

namespace App\Enums;

enum TariffListVersion: string
{
    case DRAFT = 'Draft';
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case SCHEDULED = 'Scheduled';
    case EXPIRED = 'Expired';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft Version',
            self::ACTIVE => 'Active Version',
            self::INACTIVE => 'Inactive Version',
            self::SCHEDULED => 'Scheduled Version',
            self::EXPIRED => 'Expired Version',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::INACTIVE => 'red',
            self::SCHEDULED => 'blue',
            self::EXPIRED => 'orange',
        };
    }

    public function canBeUsedForCalculation(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED]);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this, [self::DRAFT, self::INACTIVE, self::EXPIRED]);
    }
}
