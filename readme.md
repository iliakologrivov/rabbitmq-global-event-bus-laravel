# Шина событий (event-bus)

Пакет для работы с событийной шины, для прослушивания и обработки событий от сторонних сервисов во внутренней инфраструктуре.

## Установка

```bash
composer require iliakologrivov/rabbitmq-global-event-bus-laravel
./artisan vendor:publish --provider="IliaKologrivov\RabbitMQGlobalEventBusLaravel\EventBusPublishesServiceProvider"
```

Добавить добавить провайдер в config/app.php
```php
<?php
...
'providers' => [
    //...
    App\Providers\EventBusServiceProvider::class,
],
```

## Подписка на события
Для подписки на события необходимо в сервис провайдере в property event добавить запись, где ключ это полное имя события в значение класс события в текущем проекте

## Получение и обработка событий
Для получения событий необходимо запустить команду php artisan event_bus:worker
Команда получает события из шины, создаёт классы события по карте $events сервис провайдера и далее все происходит стандартными методами фреймворка
Для обработки событий необходимо добавить запись в EventServiceProvider с описанием события и listener`a 

## Отправка событий
Для отправки событий необходимо создать класс события от IliaKologrivov\RabbitMQGlobalEventBus\Sender\EventContract
Отправка события производится через IliaKologrivov\RabbitMQGlobalEventBus\Sender\Sender::send()
```php
$event = new class implements IliaKologrivov\RabbitMQGlobalEventBus\Sender\Event {
    public function getName(): string
    {
        return 'example';
    }

    public function getPayload(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return \DateTimeImmutable
     *
     * @throws \Exception
     */
    public function getTimestamp(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

};

app(IliaKologrivov\RabbitMQGlobalEventBus\Sender\Sender::class)->send($event);
```
