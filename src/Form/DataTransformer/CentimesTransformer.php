<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value === null ? null : $value / 100;
    }
    public function reverseTransform($value)
    {
        return $value === null ? null : $value * 100;
    }
}
