<?php

namespace Pact\Service\Validation;

use Pact\Exception\InvalidArgumentException;

class NumBetweenValidator 
{
    /**
     * Validate that value inside minmax limits (inclusion)
     * 
     * @param int comparing value
     * @param int minimum
     * @param int maximum
     */
    public function validate(?int $value, int $min, int $max)
    {
        if ($value === null || $value >= $min && $value <= $max) {
            return;
        }

        $msg = "Value must be between ${min} and ${max}. Given ${value}";
        throw new InvalidArgumentException($msg);
    }
}
