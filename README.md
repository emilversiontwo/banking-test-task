<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Установка и запуск
    
* Для удобства используется диспетчер задач Taskfile
* Установите его перед запуском: https://taskfile.dev/docs/installation
* Доступ к api будет по ссылке: http://localhost:8078/api/v1/
* Так же есть коллекция для Postman [banking-test-task.postman_collection.json](banking-test-task.postman_collection.json)

1. Первый запуск проекта
* Собирает и поднимает Docker окружение, устанавливает зависимости через Composer, выполняет миграции и сидеры
   ```bash
   task setup
   ```
   
2. Поднятие или пересборка Docker окружения
* Используйте, если хотите пересобрать контейнеры или просто запустить проект
    ```bash
    task up
    ```

3. Удаление Docker окружения
* Останавливает и удаляет контейнеры проекта
    ```bash
    task down
    ```

4. Запуск произвольной artisan команды
* Пример: task artisan -- migrate:fresh
    ```bash
    task artisan --
    ```
5. Запуск произвольной Composer команды
* Пример: task composer -- install
    ```bash
    task composer --
    ```
  
6. Запуск тестов
* Запускает тесты через Laravel artisan
    ```bash
    task artisan -- test
    ```
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
