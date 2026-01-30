<?php

namespace Pact\Service\Validation;

use Pact\Exception\InvalidArgumentException;

class UnifiedValidator
{
    public function validate($isInvalid, $msg)
    {
        if ($isInvalid) {
            throw new InvalidArgumentException($msg);
        }
    }
}
