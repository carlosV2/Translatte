<?php

namespace carlosV2\Translatte;

interface TranslationInterface
{
    /**
     * @param mixed $message
     *
     * @return bool
     */
    public function matches($message);

    /**
     * @param mixed $message
     *
     * @return mixed
     */
    public function translate($message);
}
