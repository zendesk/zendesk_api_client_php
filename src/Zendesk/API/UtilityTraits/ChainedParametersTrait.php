<?php

namespace Zendesk\API\UtilityTraits;

/**
 * The chained parameters trait which has helper methods for getting the parameters passed through chaining.
 *
 * An example would be a call `$client->ticket(2)->comments(3)->author();` would create an Author object with
 * chained parameters (An Array):
 *  [
 *      'Zendesk\API\Tickets' => 2,
 *      'Zendesk\API\Comments' => 3
 *  ]
 *
 * @package Zendesk\API
 *
 */

 trait ChainedParametersTrait
 {
     /**
      * @var array
      */
     protected $chainedParameters = [];

     /**
      * Sets the chained parameters
      *
      * @param $params
      *
      * @return $this
      *
      */
     public function setChainedParameters($params)
     {
         $this->chainedParameters = $params;

         return $this;
     }

     /**
      * Returns chained parameters
      *
      * @return $this
      */
     public function getChainedParameters()
     {

         return $this->chainedParameters;
     }


     /**
      * A helper method to add the chained parameters to the existing parameters.
      *
      * @param array $params The existing parameters
      * @param array $map An array describing what parameter key corresponds to which classId
      *     e.g. ['ticket_id' => 'Zendesk\API\Ticket']
      *     normal usage would be ['id' => $this::class]
      *
      * @return array
      */
     public function addChainedParametersToParams($params, $map)
     {
        $chainedParameters = $this->getChainedParameters();
         foreach ($map as $key => $className) {
             if (array_key_exists($className, $chainedParameters)) {
                 $params[$key] = $chainedParameters[$className];
             }
         }
         return $params;
     }

 }