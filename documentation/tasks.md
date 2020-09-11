# HTTP-методы приложения

### Работа с сущностью Task

##### 1. Получить список задач с фильтрацией и сортировкой
___
```sh
GET https://example.com/api/tasks
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
        "self": "http://example.com/api/tasks"
    },
    "data": [
        {
            "type": "tasks",
            "id": "13",
            "attributes": {
                "title": "Dion Graham",
                "description": "Est suscipit saepe architecto nihil ad voluptas. Iste iusto voluptatibus eveniet ut sint. Nam quia odit consequatur consequuntur similique.",
                "estimate": 97,
                "spent": 34,
                "created_at": "2020-09-10T13:32:40.000000Z",
                "updated_at": "2020-09-10T13:32:40.000000Z"
            }
        },
    ]
}
```

В запросе можно указать следующие параметры:

* Выбрать атрибуты, которые следует показывать в сущности Tasks
```sh
/api/tasks/{id}?fields[tasks]=
```
Список доступных параметров:
1. `users`
2. `title`
3. `desctiption`
4. `estimate`
5. `spent`
6. `status_id`
7. `created_at`
8. `updated_at`

* Добавить показ связанных сущностей

```sh
/api/tasks/{id}?include=
```
Список доступных связанных сущностей:

1. `users` - пользователь
2. `statuses` - статус задачи

* Добавить сортировку по полю
```sh
/api/tasks/{id}?sort=
```
Список доступных полей для сортировки:

1. `estimate` - ASC, сортировка по возрастанию значения estimate
2. `-estimate` - DESC, сортировка по убыванию значения estimate
3. `spent` - ASC, сортировка по возрастанию значения spent
4. `-spent` - DESC, сортировка по убыванию значения spent


* Добавить фильтрацию данных
```sh
/api/tasks/{id}?filter[param]=
```

Список доступных параметров для фильтрации:
1. `user_id`
2. `title`
3. `estimate`
4. `spent`
5. `status_id`

* Добавить пагинацию
```sh
/api/tasks/{id}?page[limit]=2&page[number]=10
```
Где `limit` - количество записей в порции, а `number` - номер страницы. Вместо параметра `number` может использоваться параметр `offset` - отступ (ID последней записи в предыдущей порции), новая порция будет со следующей после неё записи.

Пример ответа с пагинацией:
```sh
{
    "links": {
        "self": "http://example.com/api/task/",
        "first": "http://example.com/api/tasks?page%5Blimit%5D=2",
        "prev": "http://example.com/api/tasks?page%5Blimit%5D=2",
        "next": "http://example.com/api/tasks?page%5Blimit%5D=2&page%5Boffset%5D=4",
        "last": "http://example.com/api/tasks?page%5Blimit%5D=2&page%5Boffset%5D=100"
    },
    "data": [
        {
            "type": "tasks",
            "id": "3",
            "attributes": {
                "title": "Mrs. Kylie Ryan",
                "description": "Aut sint sed ex quo quas. Debitis vel amet nemo et quos laborum. Placeat ut placeat aut voluptatibus impedit.",
                "estimate": 40,
                "spent": 56,
                "created_at": "2020-09-10T13:32:39.000000Z",
                "updated_at": "2020-09-10T13:32:39.000000Z"
            }
        },
        {
            "type": "tasks",
            "id": "4",
            "attributes": {
                "title": "Werner Kuhn",
                "description": "Tenetur enim molestiae et iste placeat et cumque dignissimos. Nesciunt et et non quia eos. Ut sit doloribus eius voluptas.",
                "estimate": 67,
                "spent": 71,
                "created_at": "2020-09-10T13:32:39.000000Z",
                "updated_at": "2020-09-10T13:32:39.000000Z"
            }
        }
    ]
}
```

##### 2. Получить одну задачу по ID
___

```sh
GET https://example.com/api/tasks/{id}
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
        "self": "https://example.com/api/tasks/1"
    },
    "data": {
        "type": "tasks",
        "id": "1",
        "attributes": {
            "title": "Dr. Dell Lesch",
            "description": "Qui et dicta natus tempore animi vero. Ipsam maxime nisi aut deleniti.",
            "estimate": 90,
            "spent": 109,
            "created_at": "2020-09-10T13:32:39.000000Z",
            "updated_at": "2020-09-10T13:32:39.000000Z"
        }
    }
}
```

В запросе можно указать следующие параметры:

* Выбрать атрибуты, которые следует показывать в сущности Tasks
```sh
/api/tasks/{id}?fields[tasks]=
```
Список доступных параметров:
1. `users`
2. `title`
3. `desctiption`
4. `estimate`
5. `spent`
6. `status_id`
7. `created_at`
8. `updated_at`

* Добавить показ связанных сущностей

```sh
/api/tasks/{id}?include=
```
Список доступных связанных сущностей:

1. `users` - пользователь
2. `statuses` - статус задачи

Пример ответа с ошибкой:
Статус ответа - `404 Not Found`
```sh
{
    "errors": [
        {
            "status": "404",
            "source": {
                "pointer": "tasks"
            },
            "title": "Not found",
            "detail": "Task with ID {id} not found"
        }
    ]
}
```

##### 3. Создать новую задачу 
___
```sh
POST https://example.com/api/tasks
```
Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Тело запроса: 
| Свойство | Тип | Обязательное
| ------ | ------ | ------ | 
| user_id | Integer | + |
| title | String | + |
| description | Integer | + |
| estimate | Integer | + |

Пример успешного ответа:
Статус ответа - `201 Created`
```sh
{
    "links": {
        "self": "https://example.com/api/tasks/102"
    },
    "data": {
        "type": "tasks",
        "id": "102",
        "attributes": {
            "title": "Title of new task",
            "description": "Description of new task",
            "estimate": 60,
            "spent": null,
            "created_at": "2020-09-11T13:28:35.000000Z",
            "updated_at": "2020-09-11T13:28:35.000000Z"
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
                "pointer": "tasks"
            },
            "title": "Internal Server Error",
            "detail": "Ошибка при сохранении сущности Task"
        }
    ]
}
```

##### 4. Редактировать задачу
___
```sh
PATCH https://example.com/api/tasks/{id}
```
Заголовки запроса:
| Имя заголовка | Значение заголовка |
| ------ | ------ |
| Content-Type | application/vnd.api+json |

Тело запроса: 
| Свойство | Тип | Обязательное
| ------ | ------ | ------ | 
| user_id | Integer | - |
| title | String | - |
| description | Integer | - |
| estimate | Integer | - |
| spent | Integer | - |
| status_id | Integer | - |

Пример успешного ответа:
Статус ответа - `200 ОК`
```sh
{
    "links": {
        "self": "https://example.com/api/tasks/102"
    },
    "data": {
        "type": "tasks",
        "id": "102",
        "attributes": {
            "title": "Title of new task",
            "description": "Description of new task",
            "estimate": 60,
            "spent": 45,
            "created_at": "2020-09-11T13:28:35.000000Z",
            "updated_at": "2020-09-11T13:28:35.000000Z"
        }
    }
}
```

Возможные ошибки:
* `404 Not Found` - Запись сущности Task с переданным ID не найдена
* `500 Internal Server Error`Ошибка при сохранении сущности Task

##### 5. Удалить задачу
___
```sh
DELETE https://example.com/api/tasks/{id}
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
                "pointer": "tasks"
            },
            "title": "Not Found",
            "detail": "Task with ID {id} not found"
        }
    ]
}
```

[Назад](http.md)
