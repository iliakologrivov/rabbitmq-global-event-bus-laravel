<?php

declare(strict_types=1);

namespace App\Providers;

use IliaKologrivov\RabbitMQGlobalEventBusLaravel\EventBusServiceProvider as BaseServiceProvider;

class EventBusServiceProvider extends BaseServiceProvider
{
    protected $events = [
        //'event.name' => EventClass::class,
    ];

    protected $middleware = [
        //MiddlewareClass::class
    ];

    public function boot()
    {
        //
    }
}
