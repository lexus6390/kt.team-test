# Запуск приложения 

Для того, чтобы получить работающее приложение следует выполнить следущие шаги:

1. Клонировать репозитий

```sh
cd projectDir
git clone https://github.com/lexus6390/kt.team-test .
``` 

2. Подтянуть завимости проекта через Composer

```sh
composer install
```

3. В корне проекта создать файл `.env`. Там же в корне проекта находится образец заполнения файла - `.env.example`
4. В файле `.env` заполнить поля, касающиеся базы данных и URL-адрес приложения

```sh
APP_URL=http://myapplication.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testdb
DB_USERNAME=root
DB_PASSWORD=
```

5. Запустить миграции базы данных

```sh
php artisan migrate
```

6. Для заполнения базы данных тестовыми данными выполнить команду:

```sh
php artisan db:seed
```

7. Настроить сервер, чтобы он смотрел в директорию `public` проекта:

```sh
/var/www/projectDir/public
```

После этого приложение готово принимать запросы.

[Назад](/README.md)
