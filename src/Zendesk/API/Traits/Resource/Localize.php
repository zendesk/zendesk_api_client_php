<?php

namespace Zendesk\API\Traits\Resource;

trait Localize
{
    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        if (is_string($locale)) {
            $this->locale = $locale;
        }

        return $this;
    }
}
