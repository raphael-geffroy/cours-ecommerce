<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return  [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }
    public function amount(int $value, string $symbol = '€')
    {
        $final_value = number_format($value / 100, 2, ",", " ");
        return "$final_value $symbol";
    }
}
