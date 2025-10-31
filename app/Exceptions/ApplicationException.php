<?php

namespace App\Exceptions;

use Exception;

class ApplicationException extends Exception
{
    public static function alreadyExists(string $applicantName): self
    {
        return new self("Application for {$applicantName} already exists.");
    }

    public static function notFound(string $applicationId): self
    {
        return new self("Application {$applicationId} not found.");
    }

    public static function cannotReapply(string $reason): self
    {
        return new self("Cannot reapply: {$reason}");
    }

    public static function invalidStatus(string $currentStatus, string $newStatus): self
    {
        return new self("Cannot transition from {$currentStatus} to {$newStatus}.");
    }

    public static function insufficientBudget(float $required, float $available): self
    {
        return new self("Insufficient budget. Required: ₱{$required}, Available: ₱{$available}");
    }

    public static function invalidExpenseAmount(float $amount): self
    {
        return new self("Invalid expense amount: ₱{$amount}");
    }

    public static function noActiveTariff(string $serviceType): self
    {
        return new self("No active tariff found for service: {$serviceType}");
    }

    public static function expenseRangeNotFound(float $amount): self
    {
        return new self("No expense range found for amount: ₱{$amount}");
    }

    public static function duplicateApplication(string $patientName): self
    {
        return new self("Duplicate application detected for patient: {$patientName}");
    }
}
