<?php

namespace Zendesk\API;

/**
 * The Instantiator trait which has the magic methods for instatiating Resources
 * @package Zendesk\API
 *
 */

 trait InstantiatorTrait {
     /**
      * Generic method to object getter. Since all objects are protected, this method
      * exposes a getter function with the same name as the protected variable, for example
      * $client->tickets can be referenced by $client->tickets()
      *
      * @param $name
      * @param $arguments
      *
      * @throws CustomException
      */
     public function __call($name, $arguments)
     {
         $namePlural = $name . 's'; // try pluralize
         if (isset($this->$name)) {
             $class = $this->$name;
         } elseif (isset($this->$namePlural)) {
             $class = $this->$namePlural;
         } else {
             throw new CustomException("No method called $name available in " . __CLASS__);
         }

         $chainedParams = ($this instanceof ResourceAbstract) ? $this->getChainedParameters() : [];

         if ((isset($arguments[0])) && ($arguments[0] != null)) {
             $chainedParams = array_merge($chainedParams, [get_class($class) => $arguments[0]]);
         }

         $class = $class->setChainedParameters($chainedParams);

         return $class;
     }

 }
