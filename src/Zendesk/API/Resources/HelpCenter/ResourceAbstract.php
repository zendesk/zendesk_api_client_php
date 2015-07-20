<?php

namespace Zendesk\API\Resources\HelpCenter;

class ResourceAbstract extends \Zendesk\API\Resources\ResourceAbstract
{
    protected function setUpRoutes()
    {
        $this->setRoute('updateSourceLocale', "{$this->resourceName}/{categoryId}/source_locale.json");
    }

    protected function getResourceNameFromClass()
    {
        $resourceName = parent::getResourceNameFromClass();

        return 'help_center/' . $resourceName;
    }
}
