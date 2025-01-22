### Установка и запуск:

Docker:

```bash
docker-compose up -d --build
```

Composer:

```bash
composer install
```

Запуск миграции:

```bash
php artisan migrate
```

Запускаем скрипт для отправки сообщения с файлом на почту, по умолчанию отправляется файл на 100к записей:

```bash
php artisan email:send-message
```

Кол-во можно поменять указав другой файл: `test_products_1kk.zip` в классе `SendEmail`.

Сканируем почту и загружаем файл в БД:

```bash
php artisan email:upload-attachments
```

Запустить тесты:

```bash
php artisan test
```

**Ссылки**:

PhpMyAdmin:

```code
http://localhost:8081
```

Mailpit:

```
http://localhost:8025
```
