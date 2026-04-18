<?php

declare(strict_types=1);

namespace LoanFeeCalculator\Domain\ValueObject;

final readonly class Money
{
    private function __construct(private int $cents) {}

    public static function fromFloat(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public static function ofCents(int $cents): self
    {
        return new self($cents);
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function addCents(int $cents): self
    {
        return new self($this->cents + $cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function remainderOf(int $divisorCents): int
    {
        return $this->cents % $divisorCents;
    }

    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    public function isLessThan(self $other): bool
    {
        return $this->cents < $other->cents;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->cents > $other->cents;
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }
}
