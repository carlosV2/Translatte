<?php

namespace carlosV2\Translatte\SqlToRaw;

use BadFunctionCallException;
use carlosV2\Translatte\SqlToRaw\Exception\ArgumentNotFoundException;
use carlosV2\Translatte\SqlToRaw\Exception\UnknownValueTypeException;
use carlosV2\Translatte\TranslationInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

abstract class SqlToRawTranslation implements TranslationInterface
{
    const METHOD = 'process';

    /**
     * @inheritdoc
     */
    final public function matches($message)
    {
        if (!is_string($message)) {
            return false;
        }

        $pattern = preg_quote($this->getSqlPattern(), '/');
        $pattern = preg_replace('/\\\\{[^}]+}/', '.+', $pattern);

        return preg_match(sprintf('/^%s$/', $pattern), $message) === 1;
    }

    /**
     * @return string
     */
    abstract protected function getSqlPattern();

    /**
     * @inheritdoc
     */
    final public function translate($message)
    {
        return call_user_func_array(array($this, self::METHOD), $this->getParametersFromQuery($message));
    }

    /**
     * @param string $query
     *
     * @return array
     */
    private function getParametersFromQuery($query)
    {
        $pattern = preg_quote($this->getSqlPattern(), '/');
        $pattern = str_replace(preg_quote('{*}', '/'), '.+', $pattern);

        $variables = array();
        preg_match_all('/\\\\{([^}]+)\\\\}/', $pattern, $variables);
        $variables = $variables[1];

        $values = array();
        $pattern = preg_replace('/\\\\{[^}]+}/', '(.+)', $pattern);
        preg_match(sprintf('/^%s$/', $pattern), $query, $values);

        return $this->composerParametersFromVariablesValuesAndArguments(
            $variables,
            array_values(array_slice($values, 1)),
            $this->getArgumentsFromMethod()
        );
    }

    /**
     * @param array $variables
     * @param array $values
     * @param array $arguments
     *
     * @return array
     *
     * @throws ArgumentNotFoundException
     */
    private function composerParametersFromVariablesValuesAndArguments($variables, $values, $arguments)
    {
        $parameters = array();
        foreach ($arguments as $argument) {
            foreach ($variables as $index => $variable) {
                if ($variable === $argument->name) {
                    $parameters[] = $this->getValueFromString($values[$index]);
                    continue 2;
                }
            }

            if (!$argument->isOptional()) {
                throw new ArgumentNotFoundException(get_class($this), self::METHOD, $argument->name);
            }

            $parameters[] = $argument->getDefaultValue();
        }

        return $parameters;
    }

    /**
     * @param string $text
     *
     * @return bool|int|null|string
     *
     * @throws UnknownValueTypeException
     */
    private function getValueFromString($text)
    {
        $lowerText = strtolower($text);

        if ($lowerText === 'null') {
            return null;
        } elseif ($lowerText === 'true') {
            return true;
        } elseif ($lowerText === 'false') {
            return false;
        } elseif (ctype_digit($text)) {
            return (int) $text;
        } elseif (in_array(substr($text, 0, 1), array('"', "'")) && in_array(substr($text, -1), array('"', "'"))) {
            return substr($text, 1, -1);
        } else {
            throw new UnknownValueTypeException($text);
        }
    }

    /**
     * @return ReflectionParameter[]
     *
     * @throws BadFunctionCallException
     */
    private function getArgumentsFromMethod()
    {
        try {
            $method = new ReflectionMethod($this, self::METHOD);
        } catch (ReflectionException $e) {
            throw new BadFunctionCallException(sprintf('The class `%s` does not have the required method `%s`.', get_class($this), self::METHOD));
        }

        return $method->getParameters();
    }
}
