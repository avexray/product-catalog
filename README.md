# Запуск проекта

## Требования

- Docker
- Docker Compose

### Команды для запуска

1. Запустить контейнер:

```bash
docker compose up --build
```

2. Выполненить миграции:

```bash
docker compose exec app ./vendor/bin/doctrine-migrations migrate
```

3. Запутить сидер:

```bash
docker compose exec app php -f seed.php
```

4. Открыть в браузере http://localhost