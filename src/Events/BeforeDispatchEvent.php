<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events;

class BeforeDispatchEvent
{
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }
}
