<?php

namespace tests\carlosV2\Translatte\SqlToRaw;

use carlosV2\Translatte\SqlToRaw\SqlToRawTranslation;

final class DefaultFieldArgSqlToRawTestTranslation extends SqlToRawTranslation
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    protected function getSqlPattern()
    {
        return $this->pattern;
    }

    /**
     * @return mixed[]
     */
    protected function process($field = 'default')
    {
        return func_get_args();
    }
}
