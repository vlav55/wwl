<?php

namespace Pact\Service\Validation;

use Pact\Exception\InvalidArgumentException;
use Pact\Exception\NotImplementedException;
use Pact\Service\Validation\NumBetweenValidator;
use Pact\Service\Validation\SortValidatior;

/**
 * @property SortValidation $sort
 * @property NumBetweenValidator $between
 * @property UnifiedValidator $_
 */
class ValidationFactory
{
    protected static $instance = null;

    /** @var array */
    protected $validators = [];

    /** @var array */
    protected $mapping = [
        'sort' => SortValidatior::class,
        'between' => NumBetweenValidator::class,
        '_' => UnifiedValidator::class
    ];

    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Creating new instance of service if not done yet
     * and returns it back
     * 
     * @param string Name of service
     * @throws InvalidArgumentException if given arguments are not valid
     */
    public function __call($validatorName, $args)
    {
        if (array_key_exists($validatorName, $this->validators)) {
            return $this->validators[$validatorName]->validate(...$args);
        }

        if (array_key_exists($validatorName, $this->mapping)) {
            $validator = new $this->mapping[$validatorName]();
            $this->validators[$validatorName] = $validator;

            return $validator->validate(...$args);
        }

        throw new NotImplementedException("Validator \"${validatorName}\" not impelemented");
    }
}
