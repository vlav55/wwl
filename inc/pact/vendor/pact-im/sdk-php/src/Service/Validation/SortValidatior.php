<?php

namespace Pact\Service\Validation;

use Pact\Exception\InvalidArgumentException;
use Pact\Service\Validation\ValidationInterface;

class SortValidatior
{
    /**
     * Validate sort string
     * The string must be 'asc' or 'desc'
     * 
     * @param string $sort_type
     */
    public function validate(?string $sort_type)
    {
        if (null === $sort_type ||
            0 === strcmp('asc', $sort_type) ||
            0 === strcmp('desc', $sort_type)) {
            return;
        }

        $msg = "Sorting parameter must be \"asc\" or \"desc\". \"${sort_type}\" given";
        throw new InvalidArgumentException($msg);
    }
}
