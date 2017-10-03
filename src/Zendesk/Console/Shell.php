<?php

namespace Zendesk\Console;

use Zendesk\Console\Matchers\SubResourceMatcher;
use Psy\TabCompletion\Matcher\ObjectAttributesMatcher;
use Psy\TabCompletion\Matcher\ObjectMethodsMatcher;

class Shell extends \Psy\Shell
{
    /**
     * {@inheritDoc}
     */
    protected function getTabCompletionMatchers()
    {
        $tabCompletionMatchers = parent::getTabCompletionMatchers();
        $tabCompletionMatchers[] = new SubResourceMatcher();
        return array_filter($tabCompletionMatchers, function ($matcher) {
            return ! ($matcher instanceof ObjectMethodsMatcher || $matcher instanceof ObjectAttributesMatcher);
        });
    }
}
