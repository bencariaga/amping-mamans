<?php

namespace App\Exceptions;

use Exception;

class FinanceException extends Exception
{
    public static function insufficientBudget(float $required, float $available): self
    {
        return new self("Insufficient budget. Required: ₱" . number_format($required, 2) . ", Available: ₱" . number_format($available, 2));
    }

    public static function invalidAmount(float $amount): self
    {
        return new self("Invalid amount: ₱" . number_format($amount, 2));
    }

    public static function budgetNotFound(string $budgetId): self
    {
        return new self("Budget update {$budgetId} not found.");
    }

    public static function tariffNotFound(string $tariffId): self
    {
        return new self("Tariff list {$tariffId} not found.");
    }

    public static function expenseRangeOverlap(): self
    {
        return new self("Expense ranges cannot overlap within the same service.");
    }

    public static function invalidExpenseRange(float $min, float $max): self
    {
        return new self("Invalid expense range: min (₱{$min}) must be less than max (₱{$max}).");
    }

    public static function duplicateExpenseRange(): self
    {
        return new self("Duplicate expense range detected.");
    }

    public static function cannotDeleteActiveTariff(): self
    {
        return new self("Cannot delete an active tariff list.");
    }

    public static function sponsorNotFound(string $sponsorId): self
    {
        return new self("Sponsor {$sponsorId} not found.");
    }

    public static function affiliatePartnerNotFound(string $partnerId): self
    {
        return new self("Affiliate partner {$partnerId} not found.");
    }

    public static function invalidEffectivityDate(string $date): self
    {
        return new self("Effectivity date must be at least tomorrow. Provided: {$date}");
    }

    public static function effectivityDateTaken(string $date): self
    {
        return new self("The effectivity date {$date} is already taken by another tariff list.");
    }

    public static function invalidCoveragePercent(float $percent): self
    {
        return new self("Coverage percent must be between 0 and 100. Provided: {$percent}%");
    }

    public static function cannotRemoveLastService(): self
    {
        return new self("Cannot remove the last service. A tariff list must have at least one service.");
    }

    public static function serviceAlreadyExists(string $serviceId): self
    {
        return new self("Service {$serviceId} already exists in this tariff list.");
    }
}
