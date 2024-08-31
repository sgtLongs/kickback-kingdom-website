<?php

declare(strict_types = 1);

namespace Kickback\Views;

class vDecimal
{
    public int $smallUnitValue; // value in smallest divisible unit of type
    public int $scale;
    public int $precision;

    public function __construct(int $smallUnitValue, int $scale, int $precision)
    {
        $this->smallUnitValue = $smallUnitValue;
        $this->scale = $scale;
        $this->precision = $precision;
    }

    public function toDecimal() : string
    {
        $factor = 10 ** $this->precision;
        $integerPart = intdiv($this->smallUnitValue, $factor);
        $fractionalPart = abs($this->smallUnitValue % $factor);

        $fractionalPartString = str_pad((string)$fractionalPart, $this->precision, '0', STR_PAD_RIGHT);


        return ($this->smallUnitValue < 0 ? '-' : '') . $integerPart . '.' . $fractionalPartString;
    }

    public static function fromDecimal(string $decimalValue, int $scale, int $precision) : self
    {
        $parts = explode('.', $decimalValue);
        $integerPart = (int) $parts[0];
        $fractionalPart = isset($parts[1]) ? substr($parts[1], 0, $precision) : '0';
        $fractionalPart = str_pad($fractionalPart, $precision, '0', STR_PAD_RIGHT);

        $smallUnitValue = $integerPart * (10 ** $precision) + (int) $fractionalPart;

        return new self($smallUnitValue, $scale, $precision);
    }

    public function toString(): string
    {
        return $this->formatNumber($this->toDecimal(), $this->precision);
    }

    public static function fromString(string $decimalString, int $scale, int $precision) : self
    {
        return self::fromDecimal($decimalString, $scale, $precision);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function formatNumber(string $number, int $precision): string
    {
        if (strpos($number, '.') === false) 
        {
            $number .= '.';
        }

        $parts = explode('.', $number);
        $integerPart = $parts[0];
        $fractionalPart = $parts[1];

        if (strlen($fractionalPart) > $precision) 
        {
            $fractionalPart = substr($fractionalPart, 0, $precision);
        } 
        else 
        {
            $fractionalPart = str_pad($fractionalPart, $precision, '0', STR_PAD_RIGHT);
        }

        return $integerPart . '.' . $fractionalPart;
    }
}

?>
