<?php

namespace carlosV2\Translatte\SqlToRaw\Exception;

final class ArgumentNotFoundException extends SqlToRawException
{
    /**
     * @param string $className
     * @param string $method
     * @param string $argument
     */
    public function __construct($className, $method, $argument)
    {
        parent::__construct(sprintf(
            'Argument `%s` of method `%s` on class `%s` not found in the pattern',
            $argument,
            $method,
            $className
        ));
    }
}
