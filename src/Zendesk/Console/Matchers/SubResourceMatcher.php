<?php

namespace Zendesk\Console\Matchers;

use InvalidArgumentException;
use Psy\TabCompletion\Matcher\AbstractContextAwareMatcher;
use Psy\TabCompletion\Matcher\AbstractMatcher;

class SubResourceMatcher extends AbstractContextAwareMatcher
{

    /**
     * Provide tab completion matches for readline input.
     *
     * @param array $tokens information substracted with get_token_all
     * @param array $info readline_info object
     *
     * @return array The matches resulting from the query
     */
    public function getMatches(array $tokens, array $info = [])
    {
        $input = $this->getInput($tokens);

        array_shift($tokens);
        $clientToken = array_shift($tokens);
        $objectName = str_replace('$', '', $clientToken[1]);

        try {
            $object = $this->getVariable($objectName);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        do {
            $subresourceToken = array_shift($tokens);
            if (self::tokenIs($subresourceToken, self::T_STRING)) {
                if ($subresourceToken[1] === $input) {
                    return $this->methodAttributeMatcher($object, $input);
                } elseif (array_key_exists($subresourceToken[1], $object->getValidSubResources())) {
                    $object = $object->{$subresourceToken[1]}();
                } else {
                    return [];
                }
            }
        } while ($tokens);

        if ($object && !$input) {
            return $this->methodAttributeMatcher($object);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens)
    {
        $token = array_pop($tokens);
        $prevToken = array_pop($tokens);

        switch (true) {
            case self::tokenIs($token, self::T_OBJECT_OPERATOR):
            case self::tokenIs($prevToken, self::T_OBJECT_OPERATOR):
                return true;
        }

        return false;
    }

    /**
     * Look for methods and attributes whose name matches $input
     *
     * @param $object object to look for methods and attribute that matches $input
     * @param string $input string to compare on
     * @return array of method and attribute names that match $input
     */
    private function methodAttributeMatcher($object, $input = '')
    {
        return array_merge($this->methodMatcher($object, $input), $this->attributeMatcher($object, $input));
    }

    /**
     * Look for methods whose name matches $input. Also treats the return
     * of $object->getValidSubResources() as a source of methods to compare to.
     *
     * @param $object to look for methods that matches $input
     * @param string $input to compare on
     * @return array $methods of method names that match $input
     */
    private function methodMatcher($object, $input = '')
    {
        $subresources = method_exists($object, 'getValidSubResources') ?
            array_keys($object->getValidSubResources()): [];

        $methods = array_reduce(
            array_merge($subresources, get_class_methods($object)),
            function ($acc, $var) use ($input) {
                if (AbstractMatcher::startsWith($input, $var)) {
                    $acc[] = "$var()";
                }

                return $acc;
            },
            []
        );

        return $methods;
    }

    /**
     * Look for attributes whose name matches $input
     *
     * @param $object object to look for attributes that matches $input
     * @param string $input string to compare on
     * @return array $attributes of attribute names that match $input
     */
    private function attributeMatcher($object, $input = '')
    {
        $attributes = array_filter(
            array_keys(get_class_vars(get_class($object))),
            function ($var) use ($input) {
                return AbstractMatcher::startsWith($input, $var);
            }
        );

        return $attributes;
    }
}
