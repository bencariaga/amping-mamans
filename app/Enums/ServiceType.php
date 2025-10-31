<?php

namespace App\Enums;

enum ServiceType: string
{
    case MEDICAL = 'Medical Assistance';
    case BURIAL = 'Burial Assistance';
    case EDUCATIONAL = 'Educational Assistance';
    case FINANCIAL = 'Financial Assistance';

    public function description(): string
    {
        return match($this) {
            self::MEDICAL => 'Medical and healthcare related assistance',
            self::BURIAL => 'Funeral and burial assistance',
            self::EDUCATIONAL => 'Educational support and scholarships',
            self::FINANCIAL => 'General financial assistance',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::MEDICAL => 'medical-cross',
            self::BURIAL => 'flower',
            self::EDUCATIONAL => 'graduation-cap',
            self::FINANCIAL => 'peso-sign',
        };
    }

    public function maxAmount(): int
    {
        return match($this) {
            self::MEDICAL => 50000,
            self::BURIAL => 30000,
            self::EDUCATIONAL => 20000,
            self::FINANCIAL => 15000,
        };
    }
}
