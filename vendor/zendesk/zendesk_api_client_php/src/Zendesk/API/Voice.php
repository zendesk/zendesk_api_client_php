<?php

namespace Zendesk\API;

/**
 * The Voice class is a wrapper for methods as detailed on http://developer.zendesk.com/documentation/rest_api/voice.html
 */
class Voice extends ClientAbstract {

    protected $phoneNumbers;
    protected $greetings;
    protected $stats;
    protected $agents;
    protected $tickets;

    public function __construct($client) {
        parent::__construct($client);
        $this->phoneNumbers = new VoicePhoneNumbers($client);
        $this->greetings = new VoiceGreetings($client);
        $this->stats = new VoiceStats($client);
        $this->agents = new VoiceAgents($client);
        $this->tickets = new VoiceTickets($client);
    }

    /*
     * Generic method to object getter. Since all objects are protected, this method
     * exposes a getter function with the same name as the protected variable, for example
     * $client->tickets can be referenced by $client->tickets()
     */
    public function __call($name, $arguments) {
        if(isset($this->$name)) {
            return ((isset($arguments[0])) && ($arguments[0] != null) ? $this->$name->setLastId($arguments[0]) : $this->$name);
        }
        $namePlural = $name.'s'; // try pluralize
        if(isset($this->$namePlural)) {
            return $this->$namePlural->setLastId($arguments[0]);
        } else {
            throw new CustomException("No method called $name available in ".__CLASS__);
        }
    }

}
