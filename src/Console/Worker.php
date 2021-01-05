<?php

declare(strict_types=1);

namespace IliaKologrivov\RabbitMQGlobalEventBusLaravel\Console;

use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\AfterDispatchEvent;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\BeforeDispatchEvent;
use IliaKologrivov\RabbitMQGlobalEventBusLaravel\Events\FailedEvent;
use Illuminate\Console\Command;
use IliaKologrivov\RabbitMQGlobalEventBus\Worker\Worker as WorkerService;

class Worker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events_bus:work
                                            {--queue= : The names of the queues to work}
                                            {--timeout=60 : The number of seconds a child process can run}
                                            {--sleep=3 : Number of seconds to sleep when no job is available}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Events bus listener worker';

    private $worker;

    /**
     * Create a new command instance.
     *
     * @param WorkerService $worker
     */
    public function __construct(WorkerService $worker)
    {
        $this->worker = $worker;

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
        $this->listenForEvents();

        $this->worker->daemon(
            (int)$this->option('sleep'),
            (int)$this->option('timeout'),
            $this->option('queue')
        );

        return 0;
    }

    protected function listenForEvents()
    {
        $this->laravel['events']->listen(BeforeDispatchEvent::class, function ($event) {
            $this->writeStatus(get_class($event->event), 'Processing', 'comment');
        });

        $this->laravel['events']->listen(AfterDispatchEvent::class, function ($event) {
            $this->writeStatus(get_class($event->event), 'Processed', 'info');
        });

        $this->laravel['events']->listen(FailedEvent::class, function ($event) {
            $this->writeStatus(get_class($event->exception), 'Failed', 'error');
        });
    }

    protected function writeStatus(string $eventName, string $status, string $type)
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s] %s</{$type}> %s",
            (new \DateTime())->format('Y-m-d H:i:s'),
            str_pad("{$status}:", 11),
            $eventName
        ));
    }
}
