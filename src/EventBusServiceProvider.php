<?php

declare(strict_types=1);

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel;

use IliaKologrivov\RabbitMQGlobalEventBus\EventsBusConnector;
use IliaKologrivov\RabbitMQGlobalEventBus\Formatters\JsonEventFormatter;
use IliaKologrivov\RabbitMQGlobalEventBus\Manager;
use IliaKologrivov\RabbitMQGlobalEventBus\Subscriber\Subscriber;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\EventDispatcherContract;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\HandlerExceptionContract;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\Worker;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Console\CreateGeneralExchange;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Console\LoadService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\EventsMap;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use IliaKologrivov\RabbitMQGlobalEventBus\Sender\EventHandler;
use IliaKologrivov\RabbitMQGlobalEventBus\Sender\Sender;
use Illuminate\Support\Arr;
use \IliaKologrivov\RabbitMQGlobalEventBusLaravel\Console\Worker as CommandWorker;

class EventBusServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected $events = [];

    protected $middleware = [];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/event_bus.php', 'event_bus');

        $config = $this->app->get('config')->get('event_bus');

        $this->app->singleton(EventsMap::class, function ($app) {
            return new EventsMap($this->events);
        });

        $this->app->singleton(EventDispatcherContract::class, function ($app) {
            return new EventDispatcher($app->get('events'));
        });

        $this->app->singleton(Subscriber::class, function ($app) use ($config) {
            return new Subscriber(
                $app->get(EventsBusConnector::class),
                Arr::only($config, ['service_name', 'exchange_name', 'queue_name'])
            );
        });

        $this->app->singleton(HandlerExceptionContract::class, HandlerException::class);

        $this->app->singleton(EventsBusConnector::class, function() use ($config) {
            return new EventsBusConnector(
                $config['connection']['hosts'] ?? [],
                $config['connection']['options'] ?? [],
                $config['connection']['connection'] ?? AMQPLazyConnection::class
            );
        });

        $this->app->singleton(Worker::class, function($app) use ($config) {
            return new Worker(
                $app->get(EventsBusConnector::class),
                $app->get(EventDispatcherContract::class),
                $app->get(HandlerException::class),
                $app->get($config['event_formatter'] ?? JsonEventFormatter::class),
                $app->get(EventsMap::class),
                $app->get(Subscriber::class),
                $config['service_name']
            );
        });

        $this->app->singleton(EventHandler::class, function($app) {
            $instance = new EventHandler();

            foreach ($this->middleware as $item) {
                $instance->addMiddleware($app->get($item));
            }

            return $instance;
        });

        $this->app->singleton(Sender::class, function($app) use ($config) {
            return new Sender(
                $app->get(EventsBusConnector::class),
                $app->get(EventHandler::class),
                $app->get($config['event_formatter'] ?? JsonEventFormatter::class),
                Arr::only($config, ['service_name', 'general_exchange'])
            );
        });

        $this->app->alias(Sender::class, 'event_bus_pusher');

        $this->app->singleton(Manager::class, function ($app) use ($config) {
            return new Manager(
                $app->get(EventsBusConnector::class),
                Arr::only($config, ['service_name', 'general_exchange'])
            );
        });

        $this->commands([
            CommandWorker::class,
            LoadService::class,
            CreateGeneralExchange::class,
        ]);
    }

    public function provides()
    {
        return [
            Sender::class,
            EventHandler::class,
            Worker::class,
            EventsBusConnector::class,
            HandlerExceptionContract::class,
            Subscriber::class,
            EventDispatcherContract::class,
            EventsMap::class,
            Manager::class,
        ];
    }
}
