<?php

namespace carlosV2\Translatte\Exception;

use RuntimeException;

final class TranslationNotFoundException extends RuntimeException
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct(sprintf('Translation not found for message `%s`.', $this->getStringRepresentation($message)));
    }

    /**
     * @param $message
     *
     * @return array|string
     */
    private function getStringRepresentation($message)
    {
        if (is_string($message)) {
            return $message;
        } elseif (is_numeric($message)) {
            return $message;
        } elseif (is_array($message)) {
            return json_encode(array_map(array($this, 'getStringRepresentation'), $message));
        } elseif ($message instanceof \JsonSerializable) {
            return json_encode($message);
        } elseif (is_object($message)) {
            return sprintf('%s {%s}', get_class($message), $this->getStringRepresentation((array) $message));
        } else {
            return serialize($message);
        }
    }
}
