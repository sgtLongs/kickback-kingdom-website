<?php

declare(strict_types=1);

namespace Kickback\Views;

class vPrice
{
    public int $lovelaces;

    public function __construct(mixed $adaAmount)
    {
        if(is_int($adaAmount))
        {
            $this->lovelaces = $adaAmount;
        }

        if(is_string($adaAmount))
        {
            $this->lovelaces = vPrice::adaStringToLovelace($adaAmount);
        }
        
    }

    public function returnPriceIn(string $currencyCode = 'ADA') : vDecimal
    {
        switch($currencyCode)
        {
            case 'ADA' :
                $amount = new vDecimal($this->lovelaces, 15, 6);
                return $amount;
            break;
        }
    }

    public static function adaStringToLovelace(string $amount)
    {
        $lovelaceString = str_replace('.', '', $amount);
        $lovelace = intval($lovelaceString);

        return $lovelace;
    }

    public function returnPriceWithSymbol(string $currencyCode = "ADA")
    {
        switch($currencyCode)
        {
            case "ADA":
                case 'ADA' :
                    (string)$amount = new vDecimal($this->lovelaces, 15, 6);
                    return "$".$amount." ADA";
                break;
        }
    }


}

?>
