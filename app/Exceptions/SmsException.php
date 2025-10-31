<?php

namespace App\Exceptions;

use Exception;

class SmsException extends Exception
{
    public static function sendFailed(string $recipient, string $reason): self
    {
        return new self("Failed to send SMS to {$recipient}: {$reason}");
    }

    public static function invalidPhoneNumber(string $phoneNumber): self
    {
        return new self("Invalid phone number format: {$phoneNumber}");
    }

    public static function insufficientCredits(int $required, int $available): self
    {
        return new self("Insufficient SMS credits. Required: {$required}, Available: {$available}");
    }

    public static function apiError(string $message): self
    {
        return new self("SMS API error: {$message}");
    }

    public static function messageEmpty(): self
    {
        return new self("SMS message cannot be empty.");
    }

    public static function messageTooLong(int $length, int $maxLength): self
    {
        return new self("SMS message too long. Length: {$length}, Max: {$maxLength}");
    }

    public static function recipientNotFound(string $identifier): self
    {
        return new self("Recipient not found: {$identifier}");
    }
}
