# HTTP-методы приложения

### Работа с сущностью User

##### 1. Получить одного пользователя по ID
___

```sh
GET https://example.com/api/users/{id}
```

Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Пример успешного ответа:
Статус ответа - `200 ОК`
```sh
{
    "links": {
        "self": "https://example.com/api/users/1"
    },
    "data": {
        "type": "users",
        "id": "1",
        "attributes": {
            "first_name": "Enoch Anderson",
            "last_name": "Hintz",
            "email": "wolff.katherine@example.net",
            "email_verified_at": "2020-09-10T13:32:36.000000Z",
            "remember_token": "0Gu3E8qvq9",
            "created_at": "2020-09-10T13:32:36.000000Z",
            "updated_at": "2020-09-10T13:32:36.000000Z"
        }
    }
}
```

В запросе можно указать следующие параметры:

* Выбрать атрибуты, которые следует показывать в сущности User
```sh
/api/users/{id}?fields[users]=
```
Список доступных параметров:
1. `first_name`
2. `last_name`
3. `email`
4. `email_verified_at`
5. `remember_token`
6. `created_at`
7. `updated_at`

* Добавить показ связанных сущностей

```sh
/api/users/{id}?include=
```
Список доступных связанных сущностей:

1. `tasks` - задачи пользователя
2. `roles` - роль пользователя

Пример ответа с ошибкой:
Статус ответа - `404 Not Found`
```sh
{
    "errors": [
        {
            "status": "404",
            "source": {
                "pointer": "users"
            },
            "title": "Not found",
            "detail": "User with ID {id} not found"
        }
    ]
}
```

##### 2. Создать нового пользователя 
___
```sh
POST https://example.com/api/users
```
Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Тело запроса: 
| Свойство | Тип | Обязательное
| ------ | ------ | ------ | 
| first_name | String | + |
| last_name | String | + |
| role | Integer | + |
| email | String | + |
| password | String | + |
| password_confirmation | String | + |

Пример успешного ответа:
Статус ответа - `201 Created`
```sh
{
    "links": {
        "self": "https://example.com/api/users/12"
    },
    "data": {
        "type": "users",
        "id": "12",
        "attributes": {
            "first_name": "Ivan",
            "last_name": "Ivanov",
            "email": "ivanov@mail.ru",
            "email_verified_at": null,
            "remember_token": null,
            "created_at": "2020-09-11T12:42:32.000000Z",
            "updated_at": "2020-09-11T12:42:32.000000Z"
        }
    }
}
```

Пример ответа с ошибкой:
Статус ответа - `500 Internal Server Error`
```sh
{
    "errors": [
        {
            "status": "500",
            "source": {
                "pointer": "users"
            },
            "title": "Internal Server Error",
            "detail": "Ошибка при сохранении сущности User"
        }
    ]
}
```

##### 3. Редактировать пользователя
___
```sh
PATCH https://example.com/api/users/{id}
```
Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Тело запроса: 
| Свойство | Тип | Обязательное
| ------ | ------ | ------ | 
| first_name | String | - |
| last_name | String | - |
| email | String | - |

Пример успешного ответа:
Статус ответа - `200 ОК`
```sh
{
    "links": {
        "self": "https://example.com/api/users/1"
    },
    "data": {
        "type": "users",
        "id": "1",
        "attributes": {
            "first_name": "Enoch Anderson",
            "last_name": "Hintz",
            "email": "wolff.katherine@example.net",
            "email_verified_at": "2020-09-10T13:32:36.000000Z",
            "remember_token": "0Gu3E8qvq9",
            "created_at": "2020-09-10T13:32:36.000000Z",
            "updated_at": "2020-09-10T13:32:36.000000Z"
        }
    }
}
```

Возможные ошибки:
* `404 Not Found` - Запись сущности User с переданным ID не найдена
* `500 Internal Server Error`Ошибка при сохранении сущности User

##### 4. Удалить пользователя
___
```sh
DELETE https://example.com/api/users/{id}
```
Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Пример успешного ответа:
Статус ответа - `204 Not Content`

Пример ответа с ошибкой:
Статус ответа - `404 Not Found`
```sh
{
    "errors": [
        {
            "status": "404",
            "source": {
                "pointer": "users"
            },
            "title": "Not Found",
            "detail": "User with ID {id} not found"
        }
    ]
}
```

[Назад](http.md)
