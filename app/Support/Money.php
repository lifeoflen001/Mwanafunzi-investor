<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public function __construct(public readonly int $minor, public readonly string $currency = 'TZS') {}

    public static function fromDecimal(string|int|float|null $value, string $currency = 'TZS'): self
    {
        $value = trim((string) ($value ?? '0'));
        if (! preg_match('/^-?\d+(?:\.\d{1,2})?$/', $value)) throw new InvalidArgumentException('Invalid monetary amount.');
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '-');
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '0');
        $minor = ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
        return new self($negative ? -$minor : $minor, strtoupper($currency));
    }

    public function add(self $other): self { $this->assertCurrency($other); return new self($this->minor + $other->minor, $this->currency); }
    public function subtract(self $other): self { $this->assertCurrency($other); return new self($this->minor - $other->minor, $this->currency); }
    public function multiply(int $quantity): self { return new self($this->minor * $quantity, $this->currency); }
    public function isZero(): bool { return $this->minor === 0; }
    public function toDecimal(): string
    {
        $negative = $this->minor < 0 ? '-' : '';
        $absolute = abs($this->minor);
        return $negative.intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }
    public function format(): string
    {
        $decimal = $this->toDecimal();
        [$whole, $fraction] = explode('.', ltrim($decimal, '-'));
        $whole = number_format((int) $whole);
        return $this->currency.' '.($this->minor < 0 ? '-' : '').$whole.'.'.$fraction;
    }
    private function assertCurrency(self $other): void { if ($this->currency !== $other->currency) throw new InvalidArgumentException('Currency mismatch.'); }
}
