<?php

namespace carlosV2\Translatte;

use carlosV2\Translatte\Exception\TranslationNotFoundException;

final class Translator
{
    /**
     * @var TranslationInterface[]
     */
    private $translations;

    public function __construct()
    {
        $this->translations = array();
    }

    /**
     * @param TranslationInterface $translation
     */
    public function addTranslation(TranslationInterface $translation)
    {
        $this->translations[] = $translation;
    }

    /**
     * @param mixed $message
     *
     * @return mixed
     *
     * @throws TranslationNotFoundException
     */
    public function translate($message)
    {
        foreach ($this->translations as $translation) {
            if ($translation->matches($message)) {
                return $translation->translate($message);
            }
        }

        throw new TranslationNotFoundException($message);
    }
}
