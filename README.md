# Devino Viber HTTP Client
Простой клиент для работы с Devino Viber HTTP API.
 
Документация по API: http://devino-documentation.readthedocs.io/viber-resender.html

## Установка

```
composer require superjob/devino-viber
```

## Применение

```php
$guzzle = new Client();
$client = new ViberClient($guzzle, 'login', 'password');
$client->send([new Message('79xxxxxxxx', 'Sender', 'Lorem ipsum', new TextContent('Text content'))]);
```

вернет что-то вроде

```
array(1) {
  [0] =>
  class superjob\devino\message\SendResponse#23 (2) {
    protected $code =>
    string(2) "ok"
    protected $providerId =>
    string(19) "3231334966948265985"
  }
}
```