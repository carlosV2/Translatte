<?php

namespace carlosV2\Translatte\SqlToRaw\Exception;

final class UnknownValueTypeException extends SqlToRawException
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct(sprintf('The value `%s` cannot be converted to any known value.', $value));
    }
}
