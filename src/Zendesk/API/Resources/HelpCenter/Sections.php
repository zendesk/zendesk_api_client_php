<?php

namespace Zendesk\API\Resources\HelpCenter;

use Zendesk\API\Traits\Resource\Defaults;
use Zendesk\API\Traits\Resource\Locales;

/**
 * Class Sections
 * https://developer.zendesk.com/rest_api/docs/help_center/categories
 */
class Sections extends ResourceAbstract
{
    use Defaults;
    use Locales;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'section';

    /**
     * @inheritdoc
     */
    protected function setUpRoutes()
    {
        $this->setRoute('updateSourceLocale', "{$this->resourceName}/{sectionId}/source_locale.json");
    }
}
