<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel;

use IliaKologrivov\RabbitMQGlobalEventBus\Worker\EventDispatcherContract;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\AfterDispatchEvent;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\BeforeDispatchEvent;
use Illuminate\Events\Dispatcher;

class EventDispatcher implements EventDispatcherContract
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(object $event)
    {
        $this->dispatcher->dispatch(new BeforeDispatchEvent($event));

        $result = $this->dispatcher->dispatch($event);

        $this->dispatcher->dispatch(new AfterDispatchEvent($event));

        return $result;
    }
}
