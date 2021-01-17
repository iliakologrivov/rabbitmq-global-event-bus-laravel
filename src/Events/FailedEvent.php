<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events;

use \Throwable;

class FailedEvent
{
    public $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }
}
