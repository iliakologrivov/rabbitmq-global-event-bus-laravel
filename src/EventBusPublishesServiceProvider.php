<?php

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel;

use Illuminate\Support\ServiceProvider;

class EventBusPublishesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/event_bus.php' => config_path('event_bus.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../Providers/EventBusServiceProvider.php' => app_path('Providers/EventBusServiceProvider.php'),
        ], 'config');
    }
}
