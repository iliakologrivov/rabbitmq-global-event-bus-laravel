<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events;

class FailedEvent
{
    public $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }
}
