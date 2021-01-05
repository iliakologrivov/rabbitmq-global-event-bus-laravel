<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events;

class AfterDispatchEvent
{
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }
}
