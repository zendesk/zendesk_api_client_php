<?php

namespace Zendesk\API\LiveTests;

class HelpCenterTest extends BasicTest
{
    /**
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testIterateOverHelpCenterArticles()
    {
        $iterator = $this->client->helpCenter->articles()->iterator();

        $actual = iterator_to_array($iterator);

        // Generally, there should be at least one article in the help center, even if these are just the default articles.
        $this->assertTrue(is_array($actual) && count($actual) > 0, 'Should return a non-empty array of articles.');
    }

    public function testIterateOverHelpCenterSections()
    {
        $iterator = $this->client->helpCenter->sections()->iterator();

        $actual = iterator_to_array($iterator);

        // Generally, there should be at least one section in the help center, even if these are just the default sections.
        $this->assertTrue(is_array($actual) && count($actual) > 0, 'Should return a non-empty array of sections.');
    }

    public function testIterateOverHelpCenterCategories()
    {
        $iterator = $this->client->helpCenter->categories()->iterator();

        $actual = iterator_to_array($iterator);

        // Generally, there should be at least one category in the help center, even if these are just the default categories.
        $this->assertTrue(is_array($actual) && count($actual) > 0, 'Should return a non-empty array of categories.');
    }
}
