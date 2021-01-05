<?php

declare(strict_types=1);

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel;

use IliaKologrivov\RabbitMQGlobalEventBus\Worker\HandlerExceptionContract;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\FailedEvent;
use Psr\Log\LoggerInterface;
use \Throwable;
use Illuminate\Events\Dispatcher;

class HandlerException implements HandlerExceptionContract
{
    private $logger;

    private $dispatcher;

    public function __construct(LoggerInterface $logger, Dispatcher $dispatcher)
    {
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Throwable $exception)
    {
        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
        ]);

        $this->dispatcher->dispatch(new FailedEvent($exception));
    }
}
