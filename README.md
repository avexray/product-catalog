# Product catalog

## Требования

- Docker
- Docker Compose

### Команды для запуска

1. Создать и заполнить файл .env
2. Запустить контейнер:

```bash
docker compose up --build
```

3. Выполненить миграции:

```bash
docker compose exec app ./vendor/bin/doctrine-migrations migrate
```

4. Запутить сидер:

```bash
docker compose exec app php -f seed.php
```

5. Открыть в браузере http://localhost
