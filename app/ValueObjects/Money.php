<?php

namespace App\ValueObjects;

use InvalidArgumentException;

class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'PHP')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromCents(int $cents, string $currency = 'PHP'): self
    {
        return new self($cents / 100, $currency);
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function toCents(): int
    {
        return (int) round($this->amount * 100);
    }

    public function add(Money $money): self
    {
        $this->ensureSameCurrency($money);
        return new self($this->amount + $money->amount, $this->currency);
    }

    public function subtract(Money $money): self
    {
        $this->ensureSameCurrency($money);
        return new self($this->amount - $money->amount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function divide(float $divisor): self
    {
        if ($divisor == 0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return new self($this->amount / $divisor, $this->currency);
    }

    public function percentage(float $percentage): self
    {
        return new self(($this->amount * $percentage) / 100, $this->currency);
    }

    public function isGreaterThan(Money $money): bool
    {
        $this->ensureSameCurrency($money);
        return $this->amount > $money->amount;
    }

    public function isLessThan(Money $money): bool
    {
        $this->ensureSameCurrency($money);
        return $this->amount < $money->amount;
    }

    public function equals(Money $money): bool
    {
        return $this->amount === $money->amount && $this->currency === $money->currency;
    }

    public function format(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function ensureSameCurrency(Money $money): void
    {
        if ($this->currency !== $money->currency) {
            throw new InvalidArgumentException('Currency mismatch');
        }
    }
}
