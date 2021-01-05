<?php

declare(strict_types=1);

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Console;

use IliaKologrivov\RabbitMQGlobalEventBus\Manager;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\AfterDispatchEvent;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\BeforeDispatchEvent;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\FailedEvent;
use Illuminate\Console\Command;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\Worker as WorkerService;

class CreateGeneralExchange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events_bus:create-general-exchange
                                                                {--name=events_bus : Name for general exchange}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Events bus create general exchange in rabbitMQ';

    /**
     * Create a new command instance.
     *
     * @param WorkerService $worker
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $this->manager->createGeneralExchange(
            $this->option('name') ?? 'events_bus'
        );

        return 0;
    }
}
